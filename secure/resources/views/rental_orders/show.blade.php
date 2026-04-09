@extends('shared.layout')

@section('customStyles')
    <link rel="stylesheet" href="{{ url('/assets/css/rental-orders.css') }}">
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="fw-bold mb-1">Vermietung #{{ $order->id }}</h1>
            <div class="text-body-secondary">{{ $order->customer_name }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rental-orders.index') }}" class="btn btn-outline-primary rounded-pill px-4">Zurück</a>
            <a href="{{ route('rental-orders.print', $order) }}" class="btn btn-outline-primary rounded-pill px-4" target="_blank">
                <i class="bi bi-printer me-2"></i>Drucken
            </a>
            <a href="{{ route('rental-orders.edit', $order) }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-pencil-square me-2"></i>Bearbeiten
            </a>
            <form method="POST" action="{{ route('rental-orders.destroy', $order) }}" onsubmit="return confirm('Möchtest du diese Vermietung endgültig löschen? Foto und Unterschrift werden ebenfalls gelöscht.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger rounded-pill px-4">
                    <i class="bi bi-trash me-2"></i>Vermietung zurück / Löschen
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any() && $errors->has('mail'))
        <div class="alert alert-warning">{{ $errors->first('mail') }}</div>
    @endif

    <div class="card ui-card mb-3">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                @php
                    $label = $order->statusLabel();
                @endphp
                @if($label !== '')
                    <span class="badge {{ $order->statusBadgeClass() }}">{{ $label }}</span>
                @else
                    <span class="text-body-secondary">-</span>
                @endif
            </div>
            <div class="text-body-secondary">
                Erstellt: {{ $order->created_at->format('d-m-Y H:i') }}
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card ui-card h-100">
                <div class="card-body">
                    <h5 class="fw-bold">Kundendaten</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Name</dt><dd class="col-sm-7">{{ $order->customer_name }}</dd>
                        <dt class="col-sm-5">Straße</dt><dd class="col-sm-7">{{ $order->customer_street ?? '-' }}</dd>
                        <dt class="col-sm-5">Wohnort</dt><dd class="col-sm-7">{{ $order->customer_city ?? '-' }}</dd>
                        <dt class="col-sm-5">Kfz.-Kennzeichen</dt><dd class="col-sm-7">{{ $order->customer_license_plate ?? '-' }}</dd>
                        <dt class="col-sm-5">Telefon</dt><dd class="col-sm-7">{{ $order->customer_phone ?? '-' }}</dd>
                        <dt class="col-sm-5">Pers.-Ausweis-Nr.</dt><dd class="col-sm-7">{{ $order->customer_id_number ?? '-' }}</dd>
                        <dt class="col-sm-5">Führerschein-Nr.</dt><dd class="col-sm-7">{{ $order->customer_driver_license_number ?? '-' }}</dd>
                        <dt class="col-sm-5">Datum</dt><dd class="col-sm-7">{{ optional($order->rental_date)->format('d-m-Y') ?? '-' }}</dd>
                        <dt class="col-sm-5">Rückgabedatum</dt><dd class="col-sm-7">{{ optional($order->return_date)->format('d-m-Y') ?? '-' }}</dd>
                        <dt class="col-sm-5">Notizen</dt>
                        <dd class="col-sm-7">
                            @if(!empty($order->notes))
                                <div class="small" style="white-space:pre-wrap;">{{ $order->notes }}</div>
                            @else
                                -
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card ui-card h-100">
                <div class="card-body">
                    <h5 class="fw-bold">Anhänge</h5>

                    <div class="mb-3">
                        <div class="fw-semibold mb-1">Foto</div>
                        @if($order->photoAttachment)
                            <a class="btn btn-sm btn-outline-primary rounded-pill px-3" href="{{ route('rental-orders.attachments.show', [$order, $order->photoAttachment]) }}" target="_blank">Foto öffnen</a>
                        @else
                            <div class="text-body-secondary">Kein Foto.</div>
                        @endif
                    </div>

                    <div class="mb-0">
                        <div class="fw-semibold mb-1">Unterschrift</div>
                        @if($order->signatureAttachment)
                            <a class="btn btn-sm btn-outline-primary rounded-pill px-3" href="{{ route('rental-orders.attachments.show', [$order, $order->signatureAttachment]) }}" target="_blank">Unterschrift öffnen</a>
                        @else
                            <div class="text-body-secondary">Keine Unterschrift.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card ui-card">
                <div class="card-body">
                    <h5 class="fw-bold">E-Mail-Verlauf</h5>
                    <div class="table-responsive">
                        <table class="table ui-table align-middle mb-0">
                            <thead>
                            <tr>
                                <th>Typ</th>
                                <th>Empfänger</th>
                                <th>Status</th>
                                <th>Datum</th>
                                <th>Fehler</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $typeMap = [
                                    'confirmation' => 'Bestätigung',
                                    'reminder' => 'Erinnerung',
                                    'overdue' => 'Überfällig',
                                ];
                            @endphp
                            @forelse($order->mailLogs as $log)
                                <tr>
                                    <td>{{ $typeMap[$log->type] ?? ucfirst($log->type) }}</td>
                                    <td>{{ $log->to_email }}</td>
                                    <td>
                                        @if($log->status === 'sent')
                                            <span class="badge bg-success">Gesendet</span>
                                        @elseif($log->status === 'failed')
                                            <span class="badge bg-danger">Fehlgeschlagen</span>
                                        @else
                                            <span class="badge bg-secondary">In Warteschlange</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($log->attempted_at)->format('d-m-Y H:i') ?? $log->created_at->format('d-m-Y H:i') }}</td>
                                    <td class="small text-body-secondary" style="max-width:420px; white-space:normal;">
                                        @if($log->status === 'failed')
                                            {{ $log->error_message ?: '-' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-body-secondary py-4">Noch keine E-Mails geloggt.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card ui-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h5 class="fw-bold mb-0">Artikelübersicht</h5>
                    </div>

                    @php($items = is_array($order->items) ? $order->items : [])
                    @php($labels = \App\Support\RentalOrderItemCatalog::labels())

                    @php($visibleItems = collect($items)->filter(fn($v) => (int)$v > 0))

                    @if($visibleItems->isEmpty())
                        <div class="text-body-secondary mt-2">Keine Artikel erfasst.</div>
                    @else
                        <div class="table-responsive mt-2">
                            <table class="table ui-table align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>Artikel</th>
                                    <th style="width:140px;">Anzahl</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($visibleItems as $k => $v)
                                    <tr>
                                        <td>{{ $labels[$k] ?? $k }}</td>
                                        <td class="fw-semibold">{{ (int)$v }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

