<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalOrderRequest;
use App\Http\Requests\UpdateRentalOrderRequest;
use App\Mail\RentalOrderConfirmationMail;
use App\Models\RentalOrder;
use App\Models\RentalOrderAttachment;
use App\Models\RentalOrderMailLog;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RentalOrderController extends Controller
{
    public function index(Request $request)
    {
        $q = RentalOrder::query();

        $orders = $q->orderByDesc('id')->paginate(20);

        return view('rental_orders.index', [
            'title' => 'Vermietungen',
            'orders' => $orders,
        ]);
    }

    public function create()
    {
        return view('rental_orders.create', [
            'title' => 'Neue Vermietung',
        ]);
    }

    public function store(StoreRentalOrderRequest $request)
    {
        $validated = $request->validated();

        $order = RentalOrder::create([
            'customer_name' => $validated['customer_name'],
            'customer_street' => $validated['customer_street'] ?? null,
            'customer_city' => $validated['customer_city'] ?? null,
            'customer_license_plate' => $validated['customer_license_plate'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_id_number' => $validated['customer_id_number'] ?? null,
            'customer_driver_license_number' => $validated['customer_driver_license_number'] ?? null,
            'rental_date' => $validated['rental_date'] ?? null,
            'return_date' => $validated['return_date'] ?? null,
            'items' => $validated['items'] ?? [],
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->storePhotoIfPresent($order, $request);
        $this->storeSignatureIfPresent($order, $validated['signature_data_url'] ?? null);

        $adminToAddress = (string) config('rental-orders.mail_to_address');
        $adminToName = (string) config('rental-orders.mail_to_name');

        $sendCompanyConfirmation = (bool) config('rental-orders.send_confirmation_email');
        $sendCustomerConfirmation = (bool) config('rental-orders.send_confirmation_email_customer');

        $anySent = false;
        $anyFailed = false;

        // 1) Internal mail (company)
        if ($sendCompanyConfirmation) {
            $mailLog = RentalOrderMailLog::create([
                'rental_order_id' => $order->id,
                'type' => 'confirmation',
                'to_email' => $adminToAddress,
                'status' => 'queued',
                'attempted_at' => now(),
            ]);

            try {
                Mail::to(new Address($adminToAddress, $adminToName))
                    ->send(new RentalOrderConfirmationMail($order));
                $mailLog->update(['status' => 'sent']);
                $anySent = true;
            } catch (\Throwable $e) {
                $mailLog->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                $anyFailed = true;
            }
        }

        // 2) Customer mail
        if ($sendCustomerConfirmation && !empty($order->customer_email)) {
            $customerLog = RentalOrderMailLog::create([
                'rental_order_id' => $order->id,
                'type' => 'confirmation',
                'to_email' => $order->customer_email,
                'status' => 'queued',
                'attempted_at' => now(),
            ]);

            try {
                Mail::to($order->customer_email)->send(new RentalOrderConfirmationMail($order));
                $customerLog->update(['status' => 'sent']);
                $anySent = true;
            } catch (\Throwable $e) {
                $customerLog->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                $anyFailed = true;
            }
        }

        if ($anySent) {
            $order->update(['confirmation_sent_at' => now()]);
        }

        // If a mail toggle is disabled, don't treat it as a failure.
        if ($anyFailed) {
            return Redirect::route('rental-orders.show', $order)
                ->withErrors(['mail' => 'Vermietung gespeichert, aber mindestens eine E-Mail konnte nicht gesendet werden.']);
        }

        return Redirect::route('rental-orders.show', $order)
            ->with('success', 'Vermietung wurde gespeichert.');
    }

    public function show(RentalOrder $rentalOrder)
    {
        $rentalOrder->load(['attachments', 'mailLogs']);

        return view('rental_orders.show', [
            'title' => 'Vermietung #'.$rentalOrder->id,
            'order' => $rentalOrder,
        ]);
    }

    public function edit(RentalOrder $rentalOrder)
    {
        $rentalOrder->load('attachments');

        return view('rental_orders.edit', [
            'title' => 'Vermietung bearbeiten #'.$rentalOrder->id,
            'order' => $rentalOrder,
        ]);
    }

    public function update(UpdateRentalOrderRequest $request, RentalOrder $rentalOrder)
    {
        $validated = $request->validated();

        $rentalOrder->update([
            'customer_name' => $validated['customer_name'],
            'customer_street' => $validated['customer_street'] ?? null,
            'customer_city' => $validated['customer_city'] ?? null,
            'customer_license_plate' => $validated['customer_license_plate'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_id_number' => $validated['customer_id_number'] ?? null,
            'customer_driver_license_number' => $validated['customer_driver_license_number'] ?? null,
            'rental_date' => $validated['rental_date'] ?? null,
            'return_date' => $validated['return_date'] ?? null,
            'items' => $validated['items'] ?? [],
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->storePhotoIfPresent($rentalOrder, $request);
        $this->storeSignatureIfPresent($rentalOrder, $validated['signature_data_url'] ?? null);

        return Redirect::route('rental-orders.show', $rentalOrder)
            ->with('success', 'Vermietung wurde gespeichert.');
    }

    public function destroy(RentalOrder $rentalOrder)
    {
        // Deleting the order cascades to attachments + mail logs, and attachment model deletes files.
        $rentalOrder->load('attachments');
        $rentalOrder->delete();

        return Redirect::route('rental-orders.index')->with('success', 'Vermietung wurde endgültig gelöscht.');
    }

    public function attachment(RentalOrder $rentalOrder, RentalOrderAttachment $attachment)
    {
        abort_unless($attachment->rental_order_id === $rentalOrder->id, 404);

        $disk = $attachment->disk;
        $path = $attachment->path;

        abort_unless(Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->response($path, $attachment->original_name ?? basename($path), [
            'Content-Type' => $attachment->mime_type ?? 'application/octet-stream',
        ]);
    }

    public function print(RentalOrder $rentalOrder)
    {
        $rentalOrder->load(['attachments']);

        return view('rental_orders.print', [
            'title' => 'Druck #'.$rentalOrder->id,
            'header' => false,
            'footer' => false,
            'order' => $rentalOrder,
        ]);
    }

    private function storePhotoIfPresent(RentalOrder $order, Request $request): void
    {
        if (!$request->hasFile('photo')) return;
        $file = $request->file('photo');
        if (!$file || !$file->isValid()) return;

        $dir = 'rental-orders/'.$order->id;
        $name = 'photo-'.Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($dir, $name, 'local');

        // Replace existing photo if any
        $order->attachments()->where('type', 'photo')->delete();

        $order->attachments()->create([
            'type' => 'photo',
            'disk' => 'local',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    private function storeSignatureIfPresent(RentalOrder $order, ?string $dataUrl): void
    {
        if (!$dataUrl) return;

        if (!preg_match('#^data:image/(?<type>png|jpeg);base64,(?<data>.+)$#', $dataUrl, $m)) {
            return;
        }

        $binary = base64_decode($m['data'], true);
        if ($binary === false) return;

        $ext = $m['type'] === 'jpeg' ? 'jpg' : 'png';
        $dir = 'rental-orders/'.$order->id;
        $name = 'signature-'.Str::uuid().'.'.$ext;
        $path = $dir.'/'.$name;

        Storage::disk('local')->put($path, $binary);

        // Replace existing signature if any
        $order->attachments()->where('type', 'signature')->delete();

        $order->attachments()->create([
            'type' => 'signature',
            'disk' => 'local',
            'path' => $path,
            'original_name' => 'signature.'.$ext,
            'mime_type' => $m['type'] === 'jpeg' ? 'image/jpeg' : 'image/png',
            'size' => strlen($binary),
        ]);
    }
}

