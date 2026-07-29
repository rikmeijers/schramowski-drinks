@extends('shared.layout')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="fw-bold mb-1">Zuletzt gelöscht</h1>
            <div class="text-body-secondary">Gelöschte Vermietungen können wiederhergestellt oder endgültig entfernt werden.</div>
        </div>
        <a href="{{ route('rental-orders.index') }}" class="btn btn-outline-primary rounded px-4">Zurück zur Übersicht</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card ui-card">
        <div class="table-responsive">
            <table class="table ui-table align-middle mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Kunde</th>
                    <th>Datum</th>
                    <th>Rückgabedatum</th>
                    <th>Gelöscht am</th>
                    <th>Offener Betrag</th>
                    <th class="text-end">Aktionen</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="fw-semibold">{{ $order->id }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ optional($order->rental_date)->format('d-m-Y') ?? '-' }}</td>
                        <td>{{ optional($order->return_date)->format('d-m-Y') ?? '-' }}</td>
                        <td>{{ optional($order->deleted_at)->format('d-m-Y H:i') ?? '-' }}</td>
                        <td>
                            @if($order->outstanding_amount !== null)
                                {{ number_format((float) $order->outstanding_amount, 2, ',', '.') }} €
                            @else
                                <span class="text-body-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end flex-wrap">
                                <form method="POST" action="{{ route('rental-orders.restore', $order->id) }}" data-prevent-double-submit>
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded px-3" data-loading-text="Wird wiederhergestellt…">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Wiederherstellen
                                    </button>
                                </form>
                                <form method="POST"
                                      action="{{ route('rental-orders.force-destroy', $order->id) }}"
                                      data-prevent-double-submit
                                      data-force-delete-form
                                      data-customer="{{ $order->customer_name }}"
                                      data-has-outstanding="{{ $order->outstanding_amount !== null ? '1' : '0' }}"
                                      data-outstanding-amount="{{ $order->outstanding_amount !== null ? number_format((float) $order->outstanding_amount, 2, ',', '.') : '' }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded px-3" data-loading-text="Wird gelöscht…">
                                        <i class="bi bi-trash me-1"></i>Endgültig löschen
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-body-secondary py-5">Keine gelöschten Vermietungen.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('customScripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-force-delete-form]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    const customer = form.getAttribute('data-customer') || '';
                    const hasOutstanding = form.getAttribute('data-has-outstanding') === '1';
                    const amount = form.getAttribute('data-outstanding-amount') || '';

                    let message = 'Möchtest du die Vermietung von „' + customer + '“ wirklich endgültig löschen? Das kann nicht rückgängig gemacht werden.';

                    if (hasOutstanding && amount !== '') {
                        message = 'Achtung: Offener Betrag ' + amount + ' € ist hinterlegt.\n\n' + message;
                    }

                    if (!window.confirm(message)) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection
