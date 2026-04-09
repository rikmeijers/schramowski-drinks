<?php

namespace App\Http\Requests;

use App\Models\RentalOrder;

class UpdateRentalOrderRequest extends StoreRentalOrderRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $fields = (array) config('rental-orders.fields', []);
        $order = $this->route('rentalOrder');

        // On update, photo is only required if no photo exists yet and config says required
        $photoEnabled = array_key_exists('photo_enabled', $fields) ? (bool) $fields['photo_enabled'] : true;
        $photoRequired = !empty($fields['photo_required']);

        if ($photoEnabled && $photoRequired) {
            if ($order instanceof RentalOrder && $order->photoAttachment) {
                // Already has a photo, so new upload is optional
                $rules['photo'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:8192'];
            }
        }

        // On update, signature is only required if no signature exists yet and config says required
        $signatureEnabled = array_key_exists('signature_enabled', $fields) ? (bool) $fields['signature_enabled'] : true;
        $signatureRequired = !empty($fields['signature_required']);

        if ($signatureEnabled && $signatureRequired) {
            if ($order instanceof RentalOrder && $order->signatureAttachment) {
                // Already has a signature, so new one is optional
                $rules['signature_data_url'] = ['nullable', 'string'];
            }
        }

        return $rules;
    }
}
