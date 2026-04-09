<div id="cookie-consent-banner" class="js-cookie-consent cookie-consent position-fixed bottom-0 start-0 end-0 pb-4 z-1 fade-in ml mr">
    <div class="d-flex justify-content-center">
        <div class="cookie-box bg-body border border-1 shadow-lg rounded-4 p-3 px-md-4 py-md-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div class="text-center text-md-start">
                <p class="cookie-consent__message mb-0 text-secondary fw-medium" style="font-size: 0.95rem;">
                    {!! __('cookie-consent::texts.message') !!}
                </p>
            </div>

            <div class="d-flex gap-2">
                <button id="cookie-decline" type="button"
                        class="btn btn-light border-0 text-muted px-3 py-2 rounded-3 fw-medium"
                        aria-label="{{ __('cookie-consent::texts.decline') }}">
                    {{ __('cookie-consent::texts.decline') }}
                </button>

                <button id="cookie-accept" type="button"
                        class="js-cookie-consent-agree cookie-consent__agree btn btn-dark border-0 px-3 py-2 rounded-3 fw-semibold"
                        aria-label="{{ __('cookie-consent::texts.agree') }}">
                    {{ __('cookie-consent::texts.agree') }}
                </button>
            </div>
        </div>
    </div>
</div>
