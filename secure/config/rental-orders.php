<?php

return [
    'mail_to_address' => env('MAIL_TO_ADDRESS', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
    'mail_to_name' => env('MAIL_TO_NAME', env('MAIL_FROM_NAME', 'Schramowski')),

    // -------------------------------
    // E-Mail toggles (company)
    // -------------------------------
    'send_confirmation_email' => (bool) env('SEND_RESERVATION_CONFIRMATION_EMAIL', true),
    'send_reminder_email' => (bool) env('SEND_RESERVATION_REMINDER_EMAIL', true),
    'send_overdue_email' => (bool) env('SEND_RESERVATION_OVERDUE_EMAIL', true),

    // -------------------------------
    // E-Mail toggles (customer)
    // -------------------------------
    'send_confirmation_email_customer' => (bool) env('SEND_RESERVATION_CONFIRMATION_EMAIL_CUSTOMER', false),
    'send_reminder_email_customer' => (bool) env('SEND_RESERVATION_REMINDER_EMAIL_CUSTOMER', false),
    'send_overdue_email_customer' => (bool) env('SEND_RESERVATION_OVERDUE_EMAIL_CUSTOMER', false),

    // For backwards compatibility: old key used across the UI.
    // This now maps to the confirmation-to-customer toggle.
    'mails_to_customer' => (bool) env('SEND_RESERVATION_CONFIRMATION_EMAIL_CUSTOMER', false),

    // Token for the daily endpoint (/daily/{token}/rental-orders)
    // If empty: endpoint is accessible without token validation (recommended for local usage).
    'daily_endpoint_token' => env('DAILY_ENDPOINT_TOKEN', ''),

    // -------------------------------
    // Form field requirement toggles
    // -------------------------------
    'fields' => [
        'customer_name_required' => (bool) env('RENTAL_ORDER_CUSTOMER_NAME_REQUIRED', true),
        'customer_phone_required' => (bool) env('RENTAL_ORDER_CUSTOMER_PHONE_REQUIRED', false),
        'customer_email_enabled' => (bool) env('RENTAL_ORDER_CUSTOMER_EMAIL_ENABLED', true),
        'customer_email_required' => (bool) env('RENTAL_ORDER_CUSTOMER_EMAIL_REQUIRED', false), // usually controlled by SEND_RESERVATION_CONFIRMATION_EMAIL_CUSTOMER
        'customer_street_required' => (bool) env('RENTAL_ORDER_CUSTOMER_STREET_REQUIRED', false),
        'customer_id_number_required' => (bool) env('RENTAL_ORDER_CUSTOMER_ID_NUMBER_REQUIRED', false),
        'customer_city_required' => (bool) env('RENTAL_ORDER_CUSTOMER_CITY_REQUIRED', false),
        'customer_driver_license_number_required' => (bool) env('RENTAL_ORDER_CUSTOMER_DRIVER_LICENSE_NUMBER_REQUIRED', false),
        'customer_license_plate_required' => (bool) env('RENTAL_ORDER_CUSTOMER_LICENSE_PLATE_REQUIRED', false),

        'rental_date_required' => (bool) env('RENTAL_ORDER_RENTAL_DATE_REQUIRED', true),
        'return_date_required' => (bool) env('RENTAL_ORDER_RETURN_DATE_REQUIRED', true),

        'notes_enabled' => (bool) env('RENTAL_ORDER_NOTES_ENABLED', true),
        'notes_required' => (bool) env('RENTAL_ORDER_NOTES_REQUIRED', false),

        'photo_enabled' => (bool) env('RENTAL_ORDER_PHOTO_ENABLED', true),
        'photo_required' => (bool) env('RENTAL_ORDER_PHOTO_REQUIRED', false),

        'signature_enabled' => (bool) env('RENTAL_ORDER_SIGNATURE_ENABLED', true),
        'signature_required' => (bool) env('RENTAL_ORDER_SIGNATURE_REQUIRED', false),
    ],
];
