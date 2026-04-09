@extends('shared.layout')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="fw-bold mb-1">Vermietungen</h1>
            <div class="text-body-secondary">Übersicht und Status aller Vermietungsformulare.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rental-orders.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-circle me-2"></i>Neue Vermietung
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any() && $errors->has('mail'))
        <div class="alert alert-warning">{{ $errors->first('mail') }}</div>
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
                    <th>Status</th>
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
                        <td>
                            @php($label = $order->statusLabel())
                            @if($label !== '')
                                <span class="badge {{ $order->statusBadgeClass() }}">{{ $label }}</span>
                            @else
                                <span class="text-body-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @php($labels = \App\Support\RentalOrderItemCatalog::labels())
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#itemsModal"
                                    data-items-modal-trigger
                                    data-items='@json($order->items ?? [])'
                                    data-item-labels='@json($labels)'>
                                Artikel
                            </button>
                            <a href="{{ route('rental-orders.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Öffnen</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-body-secondary py-5">Noch keine Vermietungen.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Items modal -->
    <div class="modal fade" id="itemsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content ui-card">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Artikel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                </div>
                <div class="modal-body" data-items-modal-body>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Schließen</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScripts')
    <script src="{{ url('/assets/js/rental-orders-items-modal.js') }}"></script>
@endsection

