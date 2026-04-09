@extends('shared.layout')

@section('customStyles')
    <link rel="stylesheet" href="{{ url('/assets/css/rental-orders.css') }}">
@endsection

@section('content')
    @php
        // Defensive defaults (prevents "Undefined variable" notices in edge cases)
        $leftItems = $leftItems ?? [];
        $rightItems = $rightItems ?? [];
    @endphp

    @php
        $mailsToCustomer = (bool) config('rental-orders.send_confirmation_email_customer');
    @endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="fw-bold mb-1">Vermietung bearbeiten #{{ $order->id }}</h1>
            <div class="text-body-secondary">Änderungen speichern und optional Foto/Unterschrift ersetzen.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rental-orders.show', $order) }}" class="btn btn-outline-primary rounded-pill px-4">Zurück</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Bitte korrigieren:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('rental-orders.update', $order) }}" enctype="multipart/form-data" class="rental-form" data-signature-form data-edit-mode>
        @csrf
        @method('PUT')

        <div class="a4-form">
            <div class="a4-header text-center">
                <div class="fw-bold" style="letter-spacing:.08em;">SCHRAMOWSKI</div>
                <div class="text-body-secondary">getränke</div>
            </div>

            <div class="small text-body-secondary mt-2">
                Pflichtfelder sind mit <span class="text-danger fw-semibold">*</span> markiert. Optionale Angaben sind entsprechend gekennzeichnet.
            </div>

            @php
                $fieldCfg = (array) config('rental-orders.fields', []);
                $reqName = !empty($fieldCfg['customer_name_required']);
                $reqPhone = !empty($fieldCfg['customer_phone_required']);
                $reqStreet = !empty($fieldCfg['customer_street_required']);
                $reqId = !empty($fieldCfg['customer_id_number_required']);
                $reqCity = !empty($fieldCfg['customer_city_required']);
                $reqDl = !empty($fieldCfg['customer_driver_license_number_required']);
                $reqPlate = !empty($fieldCfg['customer_license_plate_required']);
                $reqRentalDate = !empty($fieldCfg['rental_date_required']);
                $reqReturnDate = !empty($fieldCfg['return_date_required']);

                $notesEnabled = array_key_exists('notes_enabled', $fieldCfg) ? (bool) $fieldCfg['notes_enabled'] : true;
                $notesRequired = !empty($fieldCfg['notes_required']);

                $photoEnabled = array_key_exists('photo_enabled', $fieldCfg) ? (bool) $fieldCfg['photo_enabled'] : true;
                $photoRequired = !empty($fieldCfg['photo_required']);

                $signatureEnabled = array_key_exists('signature_enabled', $fieldCfg) ? (bool) $fieldCfg['signature_enabled'] : true;
                $signatureRequired = !empty($fieldCfg['signature_required']);

                $emailEnabled = array_key_exists('customer_email_enabled', $fieldCfg) ? (bool) $fieldCfg['customer_email_enabled'] : true;
                $emailRequired = $emailEnabled && !empty($fieldCfg['customer_email_required']);

                $optionalBadge = '<span class="small text-body-secondary">(optional)</span>';
            @endphp

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">Name {!! $reqName ? '<span class="text-danger">*</span>' : $optionalBadge !!}</label>
                    <input class="form-control" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" @if($reqName) required @endif />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tel. {!! $reqPhone ? '<span class="text-danger">*</span>' : $optionalBadge !!}</label>
                    <input class="form-control" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" @if($reqPhone) required @endif />
                </div>

                {{-- E-Mail is always shown; required if customer mails are enabled or configured as required --}}
                @if($emailEnabled)
                    <div class="col-md-6">
                        <label class="form-label">E-Mail {!! $emailRequired ? '<span class="text-danger">*</span>' : $optionalBadge !!}</label>
                        <input class="form-control" name="customer_email" type="email" value="{{ old('customer_email', $order->customer_email) }}" @if($emailRequired) required @endif />
                    </div>
                @endif

                <div class="col-md-6">
                    <label class="form-label">Straße {!! $reqStreet ? '<span class="text-danger">*</span>' : $optionalBadge !!}</label>
                    <input class="form-control" name="customer_street" value="{{ old('customer_street', $order->customer_street) }}" @if($reqStreet) required @endif />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pers.-Ausweis-Nr. {!! $reqId ? '<span class="text-danger">*</span>' : $optionalBadge !!}</label>
                    <input class="form-control" name="customer_id_number" value="{{ old('customer_id_number', $order->customer_id_number) }}" @if($reqId) required @endif />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Wohnort {!! $reqCity ? '<span class="text-danger">*</span>' : $optionalBadge !!}</label>
                    <input class="form-control" name="customer_city" value="{{ old('customer_city', $order->customer_city) }}" @if($reqCity) required @endif />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Führerschein-Nr. {!! $reqDl ? '<span class="text-danger">*</span>' : $optionalBadge !!}</label>
                    <input class="form-control" name="customer_driver_license_number" value="{{ old('customer_driver_license_number', $order->customer_driver_license_number) }}" @if($reqDl) required @endif />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kfz.-Kennzeichen {!! $reqPlate ? '<span class="text-danger">*</span>' : $optionalBadge !!}</label>
                    <input class="form-control" name="customer_license_plate" value="{{ old('customer_license_plate', $order->customer_license_plate) }}" @if($reqPlate) required @endif />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Datum {!! $reqRentalDate ? '<span class="text-danger">*</span>' : $optionalBadge !!}</label>
                    <input class="form-control" name="rental_date" type="date" value="{{ old('rental_date', optional($order->rental_date)->toDateString()) }}" @if($reqRentalDate) required @endif />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rückgabedatum {!! $reqReturnDate ? '<span class="text-danger">*</span>' : $optionalBadge !!}</label>
                    <input class="form-control" name="return_date" type="date" value="{{ old('return_date', optional($order->return_date)->toDateString()) }}" @if($reqReturnDate) required @endif />
                </div>
            </div>

            <hr class="my-4" />

            @php
                $items = is_array($order->items) ? $order->items : [];
                $catalog = \App\Support\RentalOrderItemCatalog::labels();

                $leftKeys = [
                    'zapfanlage','transportkist','kegs','drukmeter','slangen','afdruipbak','sleutel','co2',
                    'bierglazen','wijnglazen','schnapsglazen','sektglazen','kolschglazen',
                    'statafels','bankgarnituren','asbakken','dienbladen','koelkast','koeltruhe','koelwagen'
                ];
                $rightKeys = ['spueltheke','spuel_schlauch','spuel_dreieck','spuel_stopfen','spuel_wasserhahn','spuel_gardena','arbeitstheke'];

                $leftItems = collect($leftKeys)->mapWithKeys(fn($k) => [$k => $catalog[$k] ?? $k])->all();
                $rightItems = collect($rightKeys)->mapWithKeys(fn($k) => [$k => $catalog[$k] ?? $k])->all();

                // Items that are always visible and auto-fill based on transportkist count
                $transportkistDefaults = [
                    'kegs' => 2,
                    'drukmeter' => 1,
                    'slangen' => 4,
                    'afdruipbak' => 1,
                    'sleutel' => 1,
                ];

                // Spül-items: hidden by default, shown when spueltheke >= 1
                $spuelItems = ['spuel_schlauch','spuel_dreieck','spuel_stopfen','spuel_wasserhahn','spuel_gardena'];
            @endphp

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold mb-2 ml">ARTIKEL</h5>
                        <h5 class="fw-bold mb-2 mr">ANZAHL</h5>
                    </div>
                    <div class="a4-table">
                        @foreach($leftItems as $key => $label)
                            <div class="a4-row">
                                <div class="a4-cell a4-item">{{ $label }}</div>
                                <div class="a4-cell a4-qty">
                                    <input class="form-control form-control-sm" type="number" min="0" inputmode="numeric" pattern="[0-9]*" name="items[{{ $key }}]" value="{{ old('items.'.$key, $items[$key] ?? null) }}"
                                        @if(isset($transportkistDefaults[$key])) data-transportkist-default="{{ $transportkistDefaults[$key] }}" @endif
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold mb-2 ml">ARTIKEL</h5>
                        <h5 class="fw-bold mb-2 mr">ANZAHL</h5>
                    </div>
                    <div class="a4-table">
                        @foreach($rightItems as $key => $label)
                            <div class="a4-row @if(in_array($key, $spuelItems)) spuel-item @if(empty($items[$key])) d-none @endif @endif">
                                <div class="a4-cell a4-item">{{ $label }}</div>
                                <div class="a4-cell a4-qty">
                                    <input class="form-control form-control-sm" type="number" min="0" inputmode="numeric" pattern="[0-9]*" name="items[{{ $key }}]" value="{{ old('items.'.$key, $items[$key] ?? null) }}" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <hr class="my-4" />

            @if($notesEnabled)
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notizen {!! $notesRequired ? '<span class="text-danger">*</span>' : '<span class="small text-body-secondary fw-normal">(optional)</span>' !!}</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Notizen..." @if($notesRequired) required @endif>{{ old('notes', $order->notes) }}</textarea>
                    </div>
                </div>

                <hr class="my-4" />
            @endif

            <div class="row g-4">
                @if($photoEnabled)
                    <div class="col-lg-6">
                        @if($order->photoAttachment)
                            <label class="form-label fw-semibold">Foto ersetzen <span class="small text-body-secondary fw-normal">(optional)</span></label>

                            <div class="mb-2">
                                <div class="small text-body-secondary">Aktuelles Foto:</div>
                                <a href="{{ route('rental-orders.attachments.show', [$order, $order->photoAttachment]) }}" target="_blank" rel="noopener">
                                    <img
                                        src="{{ route('rental-orders.attachments.show', [$order, $order->photoAttachment]) }}"
                                        alt="Aktuelles Foto"
                                        class="img-fluid rounded border mb-3"
                                        style="max-height: 220px;"
                                    />
                                </a>

                                <input class="form-control" type="file" name="photo" accept="image/*" />

                                <div class="form-text">Nur wenn du ein neues Foto hochlädst, wird das aktuelle ersetzt.</div>
                            </div>
                        @else
                            <label class="form-label fw-semibold">Foto-Upload {!! $photoRequired ? '<span class="text-danger">*</span>' : '<span class="small text-body-secondary fw-normal">(optional)</span>' !!}</label>

                            <input class="form-control" type="file" name="photo" accept="image/*" @if($photoRequired) required @endif />

                            <div class="form-text">Wird als Bild an die Vermietung gekoppelt.</div>
                        @endif
                    </div>
                @endif
                @if($signatureEnabled)
                    <div class="col-lg-6">
                        @if($order->signatureAttachment)
                            <label class="form-label fw-semibold">Unterschrift neu setzen <span class="small text-body-secondary fw-normal">(optional)</span></label>
                        @else
                            <label class="form-label fw-semibold">Digitale Unterschrift {!! $signatureRequired ? '<span class="text-danger">*</span>' : '<span class="small text-body-secondary fw-normal">(optional)</span>' !!}</label>
                        @endif

                        @if($order->signatureAttachment)
                            <div class="mb-2">
                                <div class="small text-body-secondary">Aktuelle Unterschrift:</div>
                                <a href="{{ route('rental-orders.attachments.show', [$order, $order->signatureAttachment]) }}" target="_blank" rel="noopener">
                                    <img
                                        src="{{ route('rental-orders.attachments.show', [$order, $order->signatureAttachment]) }}"
                                        alt="Aktuelle Unterschrift"
                                        class="img-fluid rounded border bg-white mb-3"
                                        style="max-height: 140px;"
                                    />
                                </a>

                                <input type="hidden" name="signature_data_url" data-signature-data-url value="{{ old('signature_data_url') }}" />
                                <div class="signature-pad" data-signature-pad>
                                    <canvas data-signature-canvas></canvas>
                                </div>

                                <div class="form-text">Nur wenn du neu unterschreibst, wird die Unterschrift ersetzt.</div>

                                <div class="d-flex gap-2 mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-signature-clear>
                                        <i class="bi bi-eraser me-1"></i>Löschen
                                    </button>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="signature_data_url" data-signature-data-url value="{{ old('signature_data_url') }}" />
                            <div class="signature-pad" data-signature-pad>
                                <canvas data-signature-canvas></canvas>
                            </div>

                            <div class="form-text">
                                Unterschrift wird als PNG gespeichert und an die Vermietung gekoppelt.
                                @if($signatureRequired)
                                    <br>
                                    Hinweis: Unterschrift muss gesetzt werden, bevor du speichern kannst.
                                @endif
                            </div>

                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-signature-clear>
                                    <i class="bi bi-eraser me-1"></i>Löschen
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-check2-circle me-2"></i>Änderungen speichern
                </button>
            </div>

        </div>
    </form>
@endsection

@section('customScripts')
    <script src="{{ url('/assets/js/rental-orders-signature.js') }}"></script>
    <script src="{{ url('/assets/js/rental-orders-dates.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // --- Transportkist → add/subtract defaults to kegs, drukmeter, slangen, afdruipbak, sleutel ---
            const transportkistInput = document.querySelector('input[name="items[transportkist]"]');
            const defaultInputs = document.querySelectorAll('[data-transportkist-default]');
            let previousTransportkist = parseInt(transportkistInput?.value || 0, 10);

            function onTransportkistChange() {
                const newCount = Math.max(0, parseInt(transportkistInput?.value || 0, 10));
                const delta = newCount - previousTransportkist;
                if (delta === 0) return;

                defaultInputs.forEach(function (input) {
                    const base = parseInt(input.getAttribute('data-transportkist-default'), 10);
                    const current = parseInt(input.value || 0, 10);
                    const result = current + (base * delta);
                    input.value = Math.max(0, result) || '';
                });

                previousTransportkist = newCount;
            }

            if (transportkistInput) {
                transportkistInput.addEventListener('input', onTransportkistChange);
            }

            // --- Spültheke → show/hide spuel items ---
            const spuelthekeInput = document.querySelector('input[name="items[spueltheke]"]');
            const spuelRows = document.querySelectorAll('.spuel-item');

            function syncSpuelVisibility() {
                const count = parseInt(spuelthekeInput?.value || 0, 10);
                spuelRows.forEach(function (row) {
                    if (count > 0) {
                        row.classList.remove('d-none');
                    } else {
                        row.classList.add('d-none');
                        // Clear values when hiding
                        const input = row.querySelector('input[type="number"]');
                        if (input) input.value = '';
                    }
                });
            }

            if (spuelthekeInput) {
                spuelthekeInput.addEventListener('input', syncSpuelVisibility);
                // Run on load to show spuel items if spueltheke has a value
                syncSpuelVisibility();
            }
        });
    </script>
@endsection

