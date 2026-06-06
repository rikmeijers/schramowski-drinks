@extends('shared.layout')

@section('customSeoTags')
    <meta name="description" content="Übersicht von {{ config('app.name', 'App') }}.">
@endsection

@section('content')
    @php
        $activeCount = \App\Models\RentalOrder::query()
            ->where(function ($q) {
                $q->whereNull('return_date')->orWhere('return_date', '>=', now()->toDateString());
            })
            ->count();

        $overdueCount = \App\Models\RentalOrder::query()
            ->whereNotNull('return_date')
            ->where('return_date', '<', now()->toDateString())
            ->count();

        $confirmedCount = \App\Models\RentalOrder::query()
            ->whereNotNull('confirmation_sent_at')
            ->count();

        $latestOrders = \App\Models\RentalOrder::query()->orderByDesc('id')->limit(5)->get();

        // Manual email run endpoint.
        // The route is /daily/{token}/rental-orders. If DAILY_ENDPOINT_TOKEN is empty, the controller allows any token.
        $dailyToken = (string) config('rental-orders.daily_endpoint_token');
        $urlToken = $dailyToken !== '' ? $dailyToken : 'schramowski';
        $manualDailyUrl = url('/daily/'.$urlToken.'/rental-orders');
        $manualDailyDryUrl = url('/daily/'.$urlToken.'/rental-orders?dry=1');
    @endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="fw-bold mb-1">Übersicht</h1>
            <div class="text-body-secondary">Schnellzugriff und aktuelle Vorgänge.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('rental-orders.create') }}" class="btn btn-primary rounded px-4">
                <i class="bi bi-plus-circle me-2"></i>Neue Vermietung
            </a>
            <a href="{{ route('rental-orders.index') }}" class="btn btn-outline-primary rounded px-4">Alle Vermietungen</a>

            <button type="button"
                    class="btn btn-outline-secondary rounded px-4"
                    data-daily-mail-run
                    data-daily-url="{{ $manualDailyUrl }}"
                    data-daily-dry-url="{{ $manualDailyDryUrl }}">
                <i class="bi bi-envelope-check me-2"></i>E-Mails jetzt prüfen & senden
            </button>
        </div>
    </div>

    {{-- Add a small info hint about reminder timing --}}
    <div class="alert alert-info mt-3">
        <div class="fw-semibold mb-1">Wann werden E-Mails gesendet?</div>
        <div class="text-body-secondary">
            <ul class="mb-0">
                <li><strong>Erinnerung:</strong> wenn das Rückgabedatum <strong>morgen</strong> ist und noch keine Erinnerung gesendet wurde.</li>
                <li><strong>Überfällig:</strong> wenn das Rückgabedatum <strong>vor heute</strong> liegt und noch keine Überfällig-E-Mail gesendet wurde.</li>
            </ul>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card ui-card">
                <div class="card-body">
                    <div class="text-body-secondary">Aktiv</div>
                    <div class="fs-2 fw-bold">{{ $activeCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card ui-card">
                <div class="card-body">
                    <div class="text-body-secondary">Überfällig</div>
                    <div class="fs-2 fw-bold">{{ $overdueCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card ui-card">
                <div class="card-body">
                    <div class="text-body-secondary">Bestätigt</div>
                    <div class="fs-2 fw-bold">{{ $confirmedCount }}</div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-12">
            <div class="card ui-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Letzte Vermietungen</h5>
                    @if ($latestOrders->isEmpty())
                        <div class="text-body-secondary">Noch keine Vermietungen vorhanden.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table ui-table align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kunde</th>
                                    <th>Datum</th>
                                    <th>Rückgabedatum</th>
                                    <th>Status</th>
                                    <th class="text-end">Aktion</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($latestOrders as $o)
                                    <tr>
                                        <td class="fw-semibold">{{ $o->id }}</td>
                                        <td>{{ $o->customer_name }}</td>
                                        <td>{{ optional($o->rental_date)->format('d-m-Y') ?? '-' }}</td>
                                        <td>{{ optional($o->return_date)->format('d-m-Y') ?? '-' }}</td>
                                        <td>
                                            @php($label = $o->statusLabel())
                                            @if($label !== '')
                                                <span class="badge {{ $o->statusBadgeClass() }}">{{ $label }}</span>
                                            @else
                                                <span class="text-body-secondary">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-primary rounded px-3" href="{{ route('rental-orders.show', $o) }}">Öffnen</a>
                                        </td>
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

    {{-- Result modal --}}
    <div class="modal fade" id="dailyMailRunModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content ui-card">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">E-Mail-Lauf</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                </div>
                <div class="modal-body" data-daily-mail-run-modal-body>
                    <div class="text-body-secondary">Bitte warten …</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Schließen</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScripts')
    @parent
    <script>
        (function () {
            const btn = document.querySelector('[data-daily-mail-run]');
            const dryLink = document.querySelector('[data-daily-mail-run-dry]');
            const modalEl = document.getElementById('dailyMailRunModal');
            const bodyEl = document.querySelector('[data-daily-mail-run-modal-body]');
            if (!btn || !modalEl || !bodyEl) return;

            const modal = new bootstrap.Modal(modalEl);

            function esc(s) {
                return String(s ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            async function run(url) {
                bodyEl.innerHTML = '<div class="text-body-secondary">Bitte warten …</div>';
                modal.show();

                try {
                    const res = await fetch(url, {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });

                    const contentType = res.headers.get('content-type') || '';
                    const isJson = contentType.includes('application/json');
                    const data = isJson ? await res.json() : { ok: false, error: await res.text() };

                    if (!res.ok) {
                        bodyEl.innerHTML = `
                            <div class="alert alert-danger mb-0">
                                <div class="fw-semibold">Fehler (${res.status})</div>
                                <div class="small">${esc(data?.message || data?.error || 'Unbekannter Fehler')}</div>
                            </div>
                        `;
                        return;
                    }

                    bodyEl.innerHTML = `
                        <div class="row g-3 row-cols-1 row-cols-md-3 align-items-stretch">
                            <div class="col d-flex">
                                <div class="card ui-card w-100 h-100">
                                    <div class="card-body d-flex flex-column">
                                        <div class="text-body-secondary">Status</div>
                                        <div class="fs-4 fw-bold">${data.dry ? 'Dry-Run' : 'Ausgeführt'}</div>
                                        <div class="mt-auto"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col d-flex">
                                <div class="card ui-card w-100 h-100">
                                    <div class="card-body d-flex flex-column">
                                        <div class="text-body-secondary">Erinnerungen</div>
                                        <div class="fs-2 fw-bold">${Number(data.reminders ?? 0)}</div>
                                        <div class="mt-auto"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col d-flex">
                                <div class="card ui-card w-100 h-100">
                                    <div class="card-body d-flex flex-column">
                                        <div class="text-body-secondary">Überfällig</div>
                                        <div class="fs-2 fw-bold">${Number(data.overdue ?? 0)}</div>
                                        <div class="mt-auto"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-lg-6">
                                <div class="card ui-card h-100">
                                    <div class="card-body">
                                        <div class="fw-semibold mb-2">Erinnerungen (IDs)</div>
                                        ${Array.isArray(data.reminder_orders) && data.reminder_orders.length
                                            ? `<ul class="mb-0 small">${data.reminder_orders
                                                .map(o => `<li><a href=\"/rental-orders/${esc(o.id)}\" target=\"_blank\" class=\"text-decoration-none\">#${esc(o.id)}</a> – ${esc(o.customer_name || '')}${o.return_date ? ` (Rückgabe: ${esc(o.return_date)})` : ''}</li>`)
                                                .join('')}</ul>`
                                            : `<div class="text-body-secondary small">Keine.</div>`}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card ui-card h-100">
                                    <div class="card-body">
                                        <div class="fw-semibold mb-2">Überfällig (IDs)</div>
                                        ${Array.isArray(data.overdue_orders) && data.overdue_orders.length
                                            ? `<ul class="mb-0 small">${data.overdue_orders
                                                .map(o => `<li><a href=\"/rental-orders/${esc(o.id)}\" target=\"_blank\" class=\"text-decoration-none\">#${esc(o.id)}</a> – ${esc(o.customer_name || '')}${o.return_date ? ` (Rückgabe: ${esc(o.return_date)})` : ''}</li>`)
                                                .join('')}</ul>`
                                            : `<div class="text-body-secondary small">Keine.</div>`}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-body-secondary small">
                            Hinweis: Pro Vermietung wird nur einmal erinnert bzw. überfällig gemeldet (siehe E-Mail-Verlauf je Vermietung).
                        </div>
                    `;
                } catch (e) {
                    bodyEl.innerHTML = `
                        <div class="alert alert-danger mb-0">
                            <div class="fw-semibold">Fehler</div>
                            <div class="small">${esc(e?.message || e)}</div>
                        </div>
                    `;
                }
            }

            btn.addEventListener('click', () => run(btn.dataset.dailyUrl));
            if (dryLink) {
                dryLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    run(btn.dataset.dailyDryUrl);
                });
            }
        })();
    </script>
@endsection
