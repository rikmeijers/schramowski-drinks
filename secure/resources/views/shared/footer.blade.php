<div style="height: 25px;"></div>

<div class="container mobile-center">
    <section class="row row-cols-1 row-cols-sm-2 row-cols-md-5 py-4 my-4">
        <div class="col-12 col-lg-3 col-md-6 col-sm-12 mb-3">
            <a href="{{ url('/') }}" class="mb-3 link-body-emphasis text-decoration-none">
                <img src="{{ url('/assets/images/branding/icon.png') }}" width="100" alt="Icon">
            </a>
            <p class="text-body-secondary mt-3 m-0">{{ config('app.name', 'App') }}</p>
            <p class="text-body-secondary m-0 mb-2">
                <span class="badge rounded-pill version-badge fw-semibold px-3 py-2">
                    v{{ env('APP_VERSION', '1.0.0') }}
                </span>
            </p>
            <p class="text-body-secondary m-0">&copy; {{ \Carbon\Carbon::now()->year }}</p>
        </div>

        <div class="col-12 col-lg-3 col-md-6 col-sm-12 mb-3">
            <h5>Navigation</h5>
            <ul class="nav flex-column mt-3">
                @auth
                    <li class="nav-item mb-2">
                        <a href="{{ route('home') }}"
                           class="nav-link p-0 @if(request()->routeIs('home')) text-body footer-nohover @else text-body-secondary @endif">Übersicht</a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="{{ route('rental-orders.index') }}"
                           class="nav-link p-0 @if(request()->routeIs('rental-orders.*') && !request()->routeIs('rental-orders.create')) text-body footer-nohover @else text-body-secondary @endif">Vermietung</a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="{{ route('rental-orders.create') }}"
                           class="nav-link p-0 @if(request()->routeIs('rental-orders.create')) text-body footer-nohover @else text-body-secondary @endif">Neue Vermietung</a>
                    </li>

                    @can('admin')
                        <li class="nav-item mb-2"><a href="{{ route('users.index') }}" class="nav-link p-0 @if(request()->routeIs('users.*')) text-body footer-nohover @else text-body-secondary @endif">Benutzer</a></li>
                        <li class="nav-item mb-2"><a href="{{ route('register.form') }}" class="nav-link p-0 @if(request()->routeIs('register.*')) text-body footer-nohover @else text-body-secondary @endif">Benutzer hinzufügen</a></li>
                    @endcan
                @endauth

                @guest
                    <li class="nav-item mb-2"><a href="{{ route('login.form') }}" class="nav-link p-0 text-body-secondary">Anmelden</a></li>
                @endguest
            </ul>
        </div>

        <div class="col-12 col-lg-3 col-md-6 col-sm-12 mb-3">
            <h5>Angaben</h5>
            <ul class="nav flex-column mt-3">
                <li class="nav-item mb-2">Schramowski Getränke GmbH &amp; Co. KG</li>
                <li class="nav-item mb-2">Robert-Bosch-Str. 7</li>
                <li class="nav-item mb-2">52538 Selfkant – Tüddern, Deutschland</li>
            </ul>
        </div>

        <div class="col-12 col-lg-3 col-md-6 col-sm-12 mb-3">
            <h5>Kontakt</h5>
            <ul class="nav flex-column mt-3">
                <li class="nav-item mb-2"><a href="tel:+492456792" class="nav-link text-body p-0">Telefon: +49 (0)2456 792</a></li>
                <li class="nav-item mb-2"><a href="mailto:info@schramowski-getraenke.de" class="nav-link text-body p-0">E-Mail: info@schramowski-getraenke.de</a></li>
            </ul>
        </div>
    </section>
</div>
