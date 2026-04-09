<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\RentalOrderItemCatalog;

class StoreRentalOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $fields = (array) config('rental-orders.fields', []);

        $customerEmailRules = ['nullable', 'email', 'max:255'];
        if (!empty($fields['customer_email_required'])) {
            $customerEmailRules = ['required', 'email', 'max:255'];
        }

        $emailEnabled = array_key_exists('customer_email_enabled', $fields) ? (bool) $fields['customer_email_enabled'] : true;
        if (!$emailEnabled) {
            // Field is hidden/disabled in UI, so prohibit it server-side too.
            $customerEmailRules = ['prohibited'];
        }

        $nameRequired = !empty($fields['customer_name_required']);
        $phoneRequired = !empty($fields['customer_phone_required']);
        $streetRequired = !empty($fields['customer_street_required']);
        $idNumberRequired = !empty($fields['customer_id_number_required']);
        $cityRequired = !empty($fields['customer_city_required']);
        $driverLicenseRequired = !empty($fields['customer_driver_license_number_required']);
        $licensePlateRequired = !empty($fields['customer_license_plate_required']);

        $rentalDateRequired = !empty($fields['rental_date_required']);
        $returnDateRequired = !empty($fields['return_date_required']);

        $notesEnabled = array_key_exists('notes_enabled', $fields) ? (bool) $fields['notes_enabled'] : true;
        $notesRequired = !empty($fields['notes_required']);

        $photoEnabled = array_key_exists('photo_enabled', $fields) ? (bool) $fields['photo_enabled'] : true;
        $photoRequired = !empty($fields['photo_required']);

        $signatureEnabled = array_key_exists('signature_enabled', $fields) ? (bool) $fields['signature_enabled'] : true;
        $signatureRequired = !empty($fields['signature_required']);

        return [
            'customer_name' => [($nameRequired ? 'required' : 'nullable'), 'string', 'max:255'],
            'customer_street' => [($streetRequired ? 'required' : 'nullable'), 'string', 'max:255'],
            'customer_city' => [($cityRequired ? 'required' : 'nullable'), 'string', 'max:255'],
            'customer_license_plate' => [($licensePlateRequired ? 'required' : 'nullable'), 'string', 'max:50'],
            'customer_phone' => [($phoneRequired ? 'required' : 'nullable'), 'string', 'max:50'],
            'customer_email' => $customerEmailRules,
            'customer_id_number' => [($idNumberRequired ? 'required' : 'nullable'), 'string', 'max:100'],
            'customer_driver_license_number' => [($driverLicenseRequired ? 'required' : 'nullable'), 'string', 'max:100'],

            'rental_date' => [($rentalDateRequired ? 'required' : 'nullable'), 'date'],
            'return_date' => [($returnDateRequired ? 'required' : 'nullable'), 'date', 'after_or_equal:rental_date'],

            'items' => ['nullable', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:999'],

            'notes' => array_values(array_filter([
                ($notesEnabled ? ($notesRequired ? 'required' : 'nullable') : 'prohibited'),
                'string',
                'max:5000',
            ])),

            'photo' => array_values(array_filter([
                ($photoEnabled ? ($photoRequired ? 'required' : 'nullable') : 'prohibited'),
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:8192',
            ])),

            // Signature as data URL image (png)
            'signature_data_url' => array_values(array_filter([
                ($signatureEnabled ? ($signatureRequired ? 'required' : 'nullable') : 'prohibited'),
                'string',
            ])),
        ];
    }

    public function messages(): array
    {
        return [
            // Customer fields
            'customer_name.required' => 'Bitte gib einen Namen ein.',
            'customer_name.string' => 'Der Name muss eine Zeichenkette sein.',
            'customer_name.max' => 'Der Name darf nicht mehr als 255 Zeichen enthalten.',

            'customer_street.required' => 'Bitte gib eine Straße ein.',
            'customer_street.string' => 'Die Straße muss eine Zeichenkette sein.',
            'customer_street.max' => 'Die Straße darf nicht mehr als 255 Zeichen enthalten.',

            'customer_city.required' => 'Bitte gib einen Wohnort ein.',
            'customer_city.string' => 'Der Wohnort muss eine Zeichenkette sein.',
            'customer_city.max' => 'Der Wohnort darf nicht mehr als 255 Zeichen enthalten.',

            'customer_license_plate.required' => 'Bitte gib ein Kfz.-Kennzeichen ein.',
            'customer_license_plate.string' => 'Das Kfz.-Kennzeichen muss eine Zeichenkette sein.',
            'customer_license_plate.max' => 'Das Kfz.-Kennzeichen darf nicht mehr als 50 Zeichen enthalten.',

            'customer_phone.required' => 'Bitte gib eine Telefonnummer ein.',
            'customer_phone.string' => 'Die Telefonnummer muss eine Zeichenkette sein.',
            'customer_phone.max' => 'Die Telefonnummer darf nicht mehr als 50 Zeichen enthalten.',

            'customer_email.required' => 'Bitte gib eine E-Mail-Adresse ein.',
            'customer_email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
            'customer_email.max' => 'Die E-Mail-Adresse darf nicht mehr als 255 Zeichen enthalten.',
            'customer_email.prohibited' => 'Das E-Mail-Feld ist deaktiviert.',

            'customer_id_number.required' => 'Bitte gib eine Pers.-Ausweis-Nr. ein.',
            'customer_id_number.string' => 'Die Pers.-Ausweis-Nr. muss eine Zeichenkette sein.',
            'customer_id_number.max' => 'Die Pers.-Ausweis-Nr. darf nicht mehr als 100 Zeichen enthalten.',

            'customer_driver_license_number.required' => 'Bitte gib eine Führerschein-Nr. ein.',
            'customer_driver_license_number.string' => 'Die Führerschein-Nr. muss eine Zeichenkette sein.',
            'customer_driver_license_number.max' => 'Die Führerschein-Nr. darf nicht mehr als 100 Zeichen enthalten.',

            // Dates
            'rental_date.required' => 'Bitte gib ein Datum ein.',
            'rental_date.date' => 'Das Datum muss ein gültiges Datum sein.',

            'return_date.required' => 'Bitte gib ein Rückgabedatum ein.',
            'return_date.date' => 'Das Rückgabedatum muss ein gültiges Datum sein.',
            'return_date.after_or_equal' => 'Das Rückgabedatum muss nach oder gleich dem Startdatum sein.',

            // Items
            'items.array' => 'Die Artikel müssen als Liste übergeben werden.',
            'items.*.integer' => 'Die Anzahl muss eine ganze Zahl sein.',
            'items.*.min' => 'Die Anzahl darf nicht kleiner als 0 sein.',
            'items.*.max' => 'Die Anzahl darf nicht größer als 999 sein.',

            // Notes
            'notes.required' => 'Bitte gib eine Notiz ein.',
            'notes.string' => 'Die Notizen müssen eine Zeichenkette sein.',
            'notes.max' => 'Die Notizen dürfen nicht mehr als 5000 Zeichen enthalten.',
            'notes.prohibited' => 'Das Notiz-Feld ist deaktiviert.',

            // Photo
            'photo.required' => 'Bitte lade ein Foto hoch.',
            'photo.file' => 'Das Foto muss eine Datei sein.',
            'photo.mimes' => 'Das Foto muss vom Typ JPG, JPEG, PNG oder WEBP sein.',
            'photo.max' => 'Das Foto darf nicht größer als 8 MB sein.',
            'photo.uploaded' => 'Das Foto konnte nicht hochgeladen werden. Bitte stelle sicher, dass die Datei nicht größer als 8 MB ist.',
            'photo.prohibited' => 'Das Foto-Feld ist deaktiviert.',

            // Signature
            'signature_data_url.required' => 'Bitte setze eine Unterschrift.',
            'signature_data_url.string' => 'Die Unterschrift muss als Zeichenkette übergeben werden.',
            'signature_data_url.prohibited' => 'Das Unterschrift-Feld ist deaktiviert.',
        ];
    }

    public function attributes(): array
    {
        $attrs = [
            'customer_name' => 'Name',
            'customer_street' => 'Straße',
            'customer_city' => 'Wohnort',
            'customer_license_plate' => 'Kfz.-Kennzeichen',
            'customer_phone' => 'Tel.',
            'customer_email' => 'E-Mail',
            'customer_id_number' => 'Pers.-Ausweis-Nr.',
            'customer_driver_license_number' => 'Führerschein-Nr.',
            'rental_date' => 'Datum',
            'return_date' => 'Rückgabedatum',
            'notes' => 'Notizen',
            'photo' => 'Foto',
            'signature_data_url' => 'Unterschrift',
        ];

        // Map items.<key> to human-friendly labels
        foreach (RentalOrderItemCatalog::labels() as $key => $label) {
            $attrs['items.'.$key] = $label;
        }

        return $attrs;
    }
}
