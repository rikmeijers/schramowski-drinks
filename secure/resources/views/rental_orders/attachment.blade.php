@extends('shared.layout')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="fw-bold mb-1">{{ $typeLabel }}</h1>
            <div class="text-body-secondary">Vermietung #{{ $order->id }} &mdash; {{ $order->customer_name }}</div>
        </div>
        <a href="{{ route('rental-orders.show', $order) }}" class="btn btn-outline-primary rounded px-4">
            <i class="bi bi-arrow-left me-2"></i>Zurück zur Vermietung
        </a>
    </div>

    <div class="card ui-card">
        <div class="card-body text-center py-4">
            <img
                src="{{ route('rental-orders.attachments.show', [$order, $attachment]) }}?raw=1"
                alt="{{ $typeLabel }}"
                class="img-fluid rounded"
                style="max-height: 80vh;"
            >
        </div>
    </div>
@endsection
