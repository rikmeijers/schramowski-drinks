<!DOCTYPE html>
<html lang='de'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Passwort zurücksetzen – {{ $app_name }}</title>
</head>
<body style='margin:0;padding:0;font-family:"SF Pro Text","Public Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background-color:#f2f2f7;color:#1c1c1e;line-height:1.5;'>

@component('emails._layout', [
    'title' => "Passwort zurücksetzen – {$app_name}",
    'preheader' => 'Setze dein Passwort zurück, falls du es angefordert hast.',
    'brand' => $app_name,
    'subtitle' => 'Sicherheit',
])
    <h1 style="margin:0 0 10px 0;font-size:20px;line-height:1.25;color:#111;font-weight:800;">Passwort zurücksetzen</h1>

    <p style="margin:0 0 12px 0;color:#374151;font-size:14px;line-height:1.6;">Hallo {{ $email }},</p>
    <p style="margin:0 0 14px 0;color:#374151;font-size:14px;line-height:1.6;">
        wir haben eine Anfrage erhalten, dein Passwort zurückzusetzen. Wenn du das warst, klicke auf den Button unten.
        Falls nicht, kannst du diese E-Mail ignorieren.
    </p>

    @include('emails._components.button', ['url' => $action_url, 'label' => 'Passwort zurücksetzen'])

    <p style="margin:12px 0 0 0;color:#6b7280;font-size:13px;line-height:1.6;">
        Dieser Link läuft aus Sicherheitsgründen nach 15 Minuten ab.
    </p>

    <hr style="border:0;border-top:1px solid #e5e7eb;margin:18px 0;">

    <p style="margin:0;color:#6b7280;font-size:12px;line-height:1.6;">
        Wenn der Button nicht funktioniert, kopiere diesen Link in deinen Browser:<br>
        <span style="word-break:break-all;">{{ $action_url }}</span>
    </p>
@endcomponent

</body>
</html>
