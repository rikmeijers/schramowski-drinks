@extends('shared.layout')

@php($header = false)
@php($footer = false)

@section('customStyles')
    <link rel="stylesheet" href="{{ url('/assets/css/rental-orders.css') }}">
    <style>
        .print-actions { display:flex; justify-content:flex-end; gap:.5rem; margin: 1rem 0; }
        @media print {
            .print-actions { display:none !important; }

            /* Always print in light mode, even if UI is in dark mode (Bootstrap uses data-bs-theme). */
            html { color-scheme: light; }
            html[data-bs-theme],
            body[data-bs-theme],
            [data-bs-theme] {
                --bs-body-bg: #ffffff;
                --bs-body-color: #111827;
                --bs-secondary-color: #4b5563;
                --bs-tertiary-color: #6b7280;
                --bs-border-color: #d1d5db;
                --bs-card-bg: #ffffff;
                --bs-body-bg-rgb: 255,255,255;
                --bs-body-color-rgb: 17,24,39;
            }

            html, body { background: #ffffff !important; color: #111827 !important; }
            .a4-form { background: #ffffff !important; color: #111827 !important; box-shadow: none !important; }
            .text-body-secondary { color: #4b5563 !important; }
            .a4-table, .a4-cell, .a4-row { border-color: #d1d5db !important; }

            /* Make sure dark mode paper vars don't sneak in */
            :root { --a4-paper: #ffffff; --a4-border: rgba(0,0,0,.18); }
        }
    </style>
@endsection

@section('content')
    <div class="print-actions">
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-2"></i>Drucken</button>
        <a class="btn btn-outline-primary" href="{{ route('rental-orders.show', $order) }}">Zurück</a>
    </div>

    <div class="a4-form">
        <div class="a4-header text-center">
            <div class="fw-bold" style="letter-spacing:.08em;">SCHRAMOWSKI</div>
            <div class="text-body-secondary">getränke</div>
        </div>

        <div class="mt-3">
            <div class="row g-2">
                <div class="col-6"><strong>Name:</strong> {{ $order->customer_name }}</div>
                <div class="col-6"><strong>Tel.:</strong> {{ $order->customer_phone ?? '-' }}</div>
                <div class="col-6"><strong>Straße:</strong> {{ $order->customer_street ?? '-' }}</div>
                <div class="col-6"><strong>Pers.-Ausweis-Nr.:</strong> {{ $order->customer_id_number ?? '-' }}</div>
                <div class="col-6"><strong>Wohnort:</strong> {{ $order->customer_city ?? '-' }}</div>
                <div class="col-6"><strong>Führerschein-Nr.:</strong> {{ $order->customer_driver_license_number ?? '-' }}</div>
                <div class="col-6"><strong>Kfz.-Kennzeichen:</strong> {{ $order->customer_license_plate ?? '-' }}</div>
                <div class="col-3"><strong>Datum:</strong> {{ optional($order->rental_date)->format('d-m-Y') ?? '-' }}</div>
                <div class="col-3"><strong>Rückgabe:</strong> {{ optional($order->return_date)->format('d-m-Y') ?? '-' }}</div>
            </div>

            @if(!empty($order->notes))
                <div class="mt-3">
                    <div class="fw-semibold mb-1">Notizen (optional)</div>
                    <div class="small" style="white-space:pre-wrap;">{{ $order->notes }}</div>
                </div>
            @endif
        </div>

        <hr class="my-4" />

        @php($items = is_array($order->items) ? $order->items : [])
        @php($labels = \App\Support\RentalOrderItemCatalog::labels())
        @php($printRows = collect($items)
            ->map(fn($qty, $key) => ['key' => $key, 'label' => $labels[$key] ?? $key, 'qty' => (int) $qty])
            ->filter(fn($r) => $r['qty'] > 0)
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values())

        <h5 class="fw-bold mb-2">Artikelübersicht</h5>
        <div class="a4-table">
            <div class="a4-row a4-row-head" style="grid-template-columns: 1fr 140px;">
                <div class="a4-cell a4-item fw-semibold">Artikel</div>
                <div class="a4-cell a4-qty fw-semibold">Anzahl</div>
            </div>
            @foreach($printRows as $row)
                <div class="a4-row" style="grid-template-columns: 1fr 140px;">
                    <div class="a4-cell a4-item">{{ $row['label'] }}</div>
                    <div class="a4-cell a4-qty">{{ $row['qty'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mt-4">
            <div class="col-6">
                <div class="fw-semibold mb-2">Foto</div>
                @if($order->photoAttachment)
                    <img src="{{ route('rental-orders.attachments.show', [$order, $order->photoAttachment]) }}" alt="Foto" style="max-width:100%; border:1px solid var(--a4-border); border-radius:12px;" />
                @else
                    <div class="text-body-secondary">Kein Foto.</div>
                @endif
            </div>
            <div class="col-6">
                <div class="fw-semibold mb-2">Unterschrift</div>
                @if($order->signatureAttachment)
                    <img src="{{ route('rental-orders.attachments.show', [$order, $order->signatureAttachment]) }}" alt="Unterschrift" style="max-width:100%; border:1px solid var(--a4-border); border-radius:12px;" />
                @else
                    <div class="text-body-secondary">Keine Unterschrift.</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Force light theme only for print view (Bootstrap uses data-bs-theme on <html>)
        (function () {
            const html = document.documentElement;
            const body = document.body;
            const prevHtml = html.getAttribute('data-bs-theme');
            const prevBody = body.getAttribute('data-bs-theme');

            function setLight() {
                html.setAttribute('data-bs-theme', 'light');
                body.setAttribute('data-bs-theme', 'light');
            }

            function restore() {
                if (prevHtml === null) html.removeAttribute('data-bs-theme'); else html.setAttribute('data-bs-theme', prevHtml);
                if (prevBody === null) body.removeAttribute('data-bs-theme'); else body.setAttribute('data-bs-theme', prevBody);
            }

            setLight();
            window.addEventListener('beforeprint', setLight);
            window.addEventListener('afterprint', restore);
        })();
    </script>
@endsection

