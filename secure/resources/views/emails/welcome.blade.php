<!DOCTYPE html>
<html lang='de'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Willkommen bei {{ $app_name }}</title>
</head>
<body style='margin:0;padding:0;font-family:"SF Pro Text","Public Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background-color:#f2f2f7;color:#1c1c1e;line-height:1.5;'>
{{-- Layout slot --}}
@php
    // Use Blade slot pattern by outputting into $slot via @section is not available here; keep simple include.
@endphp

@component('emails._layout', [
    'title' => "Willkommen bei {$app_name}",
    'preheader' => 'Bitte bestätige deine E-Mail-Adresse, um dein Konto zu aktivieren.',
    'brand' => $app_name,
    'subtitle' => 'Willkommen',
])
    <h1 style="margin:0 0 10px 0;font-size:20px;line-height:1.25;color:#111;font-weight:800;">Willkommen bei {{ $app_name }}</h1>

    <p style="margin:0 0 12px 0;color:#374151;font-size:14px;line-height:1.6;">Hallo {{ $email }},</p>
    <p style="margin:0 0 14px 0;color:#374151;font-size:14px;line-height:1.6;">
        vielen Dank für deine Registrierung. Bitte bestätige deine E-Mail-Adresse, um dein Konto zu aktivieren.
    </p>

    @include('emails._components.button', ['url' => $action_url, 'label' => 'E-Mail bestätigen'])

    <p style="margin:12px 0 0 0;color:#6b7280;font-size:13px;line-height:1.6;">
        Der Link läuft aus Sicherheitsgründen nach 15 Minuten ab.
    </p>

    <hr style="border:0;border-top:1px solid #e5e7eb;margin:18px 0;">

    <p style="margin:0;color:#6b7280;font-size:12px;line-height:1.6;">
        Wenn der Button nicht funktioniert, kopiere diesen Link in deinen Browser:<br>
        <span style="word-break:break-all;">{{ $action_url }}</span>
    </p>
@endcomponent
</body>
</html>
