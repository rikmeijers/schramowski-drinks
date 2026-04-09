<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $title ?? config('app.name', 'App') }}</title>
</head>
<body style="margin:0;padding:0;background:#f2f2f7;color:#1c1c1e;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
        {{ $preheader ?? '' }}
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f2f2f7;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="width:600px;max-width:600px;">
                    <tr>
                        <td style="padding:0 4px 14px 4px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        <div style="font-weight:800;font-size:16px;letter-spacing:.2px;color:#111;">
                                            {{ $brand ?? config('app.name', 'App') }}
                                        </div>
                                        <div style="font-size:12px;color:#6b7280;margin-top:2px;">
                                            {{ $subtitle ?? '' }}
                                        </div>
                                    </td>
                                    <td align="right" style="vertical-align:middle;">
                                        <div style="font-size:12px;color:#6b7280;">
                                            {{ now()->format('d.m.Y') }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#ffffff;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.08);padding:24px 22px;">
                            @if (isset($slot))
                                {{ $slot }}
                            @else
                                @yield('content')
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:14px 6px 0 6px;">
                            <div style="font-size:12px;line-height:1.5;color:#6b7280;text-align:center;">
                                {!! $footer ?? ('&copy; ' . now()->year . ' ' . e(config('app.name', 'App'))) !!}
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
