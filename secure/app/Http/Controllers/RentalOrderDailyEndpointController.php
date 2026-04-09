<?php

namespace App\Http\Controllers;

use App\Mail\RentalOrderOverdueMail;
use App\Mail\RentalOrderReminderMail;
use App\Models\RentalOrder;
use App\Models\RentalOrderMailLog;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class RentalOrderDailyEndpointController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        $expected = (string) config('rental-orders.daily_endpoint_token', '');

        // If no expected token is configured, allow access (local/manual usage).
        // If configured, require the token to match.
        if ($expected !== '' && !hash_equals($expected, $token)) {
            abort(403);
        }

        $dryRun = $request->boolean('dry');
        $now = now();

        $reminderCount = 0;
        $overdueCount = 0;

        $reminderOrdersOut = [];
        $overdueOrdersOut = [];

        $adminToAddress = (string) config('rental-orders.mail_to_address');
        $adminToName = (string) config('rental-orders.mail_to_name');

        $sendCompanyReminder = (bool) config('rental-orders.send_reminder_email');
        $sendCustomerReminder = (bool) config('rental-orders.send_reminder_email_customer');

        $sendCompanyOverdue = (bool) config('rental-orders.send_overdue_email');
        $sendCustomerOverdue = (bool) config('rental-orders.send_overdue_email_customer');

        // Reminder: return_date is today or tomorrow (and no reminder yet)
        // This ensures that if the endpoint runs late (on the return date itself),
        // the reminder is still sent instead of being skipped entirely.
        $reminderOrders = RentalOrder::query()
            ->whereNotNull('return_date')
            ->whereDate('return_date', '>=', $now->toDateString())
            ->whereDate('return_date', '<=', $now->copy()->addDay()->toDateString())
            ->whereNull('reminder_sent_at')
            ->get();

        foreach ($reminderOrders as $order) {
            // internal
            if (!$dryRun && $sendCompanyReminder) {
                $log = RentalOrderMailLog::create([
                    'rental_order_id' => $order->id,
                    'type' => 'reminder',
                    'to_email' => $adminToAddress,
                    'status' => 'queued',
                    'attempted_at' => $now,
                ]);

                try {
                    Mail::to(new Address($adminToAddress, $adminToName))
                        ->send(new RentalOrderReminderMail($order));
                    $log->update(['status' => 'sent']);
                } catch (\Throwable $e) {
                    $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                }
            }

            // Set reminder_sent_at if at least company or customer reminder sending is enabled and not dry.
            if (!$dryRun && ($sendCompanyReminder || ($sendCustomerReminder && !empty($order->customer_email)))) {
                $order->update(['reminder_sent_at' => $now]);
            }

            // customer
            if (!$dryRun && $sendCustomerReminder && !empty($order->customer_email)) {
                $customerLog = RentalOrderMailLog::create([
                    'rental_order_id' => $order->id,
                    'type' => 'reminder',
                    'to_email' => $order->customer_email,
                    'status' => 'queued',
                    'attempted_at' => $now,
                ]);

                try {
                    Mail::to($order->customer_email)->send(new RentalOrderReminderMail($order));
                    $customerLog->update(['status' => 'sent']);
                } catch (\Throwable $e) {
                    $customerLog->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                }
            }

            $reminderOrdersOut[] = [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'return_date' => optional($order->return_date)->toDateString(),
            ];

            $reminderCount++;
        }

        // Overdue: return_date < today (i.e. from the day after Rückgabedatum onwards)
        // and no overdue mail sent yet. Always sends, even if days/weeks late.
        $overdueOrders = RentalOrder::query()
            ->whereNotNull('return_date')
            ->whereDate('return_date', '<', $now->toDateString())
            ->whereNull('overdue_sent_at')
            ->get();

        foreach ($overdueOrders as $order) {
            if (!$dryRun && $sendCompanyOverdue) {
                $log = RentalOrderMailLog::create([
                    'rental_order_id' => $order->id,
                    'type' => 'overdue',
                    'to_email' => $adminToAddress,
                    'status' => 'queued',
                    'attempted_at' => $now,
                ]);

                try {
                    Mail::to(new Address($adminToAddress, $adminToName))
                        ->send(new RentalOrderOverdueMail($order));
                    $log->update(['status' => 'sent']);
                } catch (\Throwable $e) {
                    $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                }
            }

            if (!$dryRun && ($sendCompanyOverdue || ($sendCustomerOverdue && !empty($order->customer_email)))) {
                $order->update(['overdue_sent_at' => $now]);
            }

            if (!$dryRun && $sendCustomerOverdue && !empty($order->customer_email)) {
                $customerLog = RentalOrderMailLog::create([
                    'rental_order_id' => $order->id,
                    'type' => 'overdue',
                    'to_email' => $order->customer_email,
                    'status' => 'queued',
                    'attempted_at' => $now,
                ]);

                try {
                    Mail::to($order->customer_email)->send(new RentalOrderOverdueMail($order));
                    $customerLog->update(['status' => 'sent']);
                } catch (\Throwable $e) {
                    $customerLog->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                }
            }

            $overdueOrdersOut[] = [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'return_date' => optional($order->return_date)->toDateString(),
            ];

            $overdueCount++;
        }

        return Response::json([
            'ok' => true,
            'dry' => $dryRun,
            'reminders' => $reminderCount,
            'overdue' => $overdueCount,
            'reminder_orders' => $reminderOrdersOut,
            'overdue_orders' => $overdueOrdersOut,
        ]);
    }
}
