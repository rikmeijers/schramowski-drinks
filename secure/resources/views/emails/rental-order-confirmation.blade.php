@extends('emails._layout')

@section('content')
    <h2 style="margin:0 0 12px 0;">Bestätigung Vermietung #{{ $order->id }}</h2>

    <p style="margin:0 0 12px 0;">Wir haben deine Vermietung erhalten und registriert.</p>

    <p style="margin:0 0 8px 0;"><strong>Kunde:</strong> {{ $order->customer_name }}</p>
    @if($order->return_date)
        <p style="margin:0 0 8px 0;"><strong>Rückgabedatum:</strong> {{ $order->return_date->format('d-m-Y') }}</p>
    @endif

    <p style="margin:16px 0 0 0;">Mit freundlichen Grüßen,<br>Schramowski Getränke</p>
@endsection
