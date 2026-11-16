<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $bike->model }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="">

    {{-- ===== BARRA DE NAVEGACIÓN ===== --}}
    @php
        $link = 'px-3 py-2 rounded-md text-sm font-medium transition';
        $on   = 'bg-indigo-100 text-indigo-700';
        $off  = 'text-gray-600 hover:bg-gray-100';
    @endphp
    <nav class="">
        <div class="">
            <a href="{{ route('bikes.index') }}" class="">
                🚲 {{ config('app.name') }}
            </a>

            <div class="">
                <a href="{{ route('bikes.index') }}"
                   class="">Catálogo</a>

                @auth
                    <a href="{{ route('bookings.index') }}"
                       class="">Mis reservas</a>
                    <a href="{{ route('profile.edit') }}"
                       class="">{{ Auth::user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="">Iniciar sesión</a>
                    <a href="{{ route('register') }}"
                       class="">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="">

        {{-- Mensajes flash --}}
        @if (session('success'))
            <div class="">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="">
                {{ session('error') }}
            </div>
        @endif

        <div class="">
            <a href="{{ route('bikes.index') }}" class="">&larr; Volver al catálogo</a>
            @auth
                <a href="{{ route('bookings.index') }}" class="">Mis reservas</a>
            @endauth
        </div>

        <div class="">
            <div class="">
                <div class="">🚲</div>
            </div>

            <div class="">
                <div class="">
                    <h1 class="">{{ $bike->model }}</h1>
                    @if ($bike->status === 'available')
                        <span class="">Disponible</span>
                    @else
                        <span class="">No disponible</span>
                    @endif
                </div>

                <p class="">Nº serie: {{ $bike->serial_number }}</p>
                <div class=""></div>

                <p class="">
                    {{ number_format($bike->price_per_hour, 2, ',', '.') }} €
                    <span class="">/ hora</span>
                </p>

                <div class="">
                    @if ($bike->status === 'available')
                        {{-- Si el visitante no ha iniciado sesión, el middleware 'auth' lo enviará al login --}}
                        <a href="{{ route('bookings.create', $bike) }}"
                           class="">
                            Reservar esta bicicleta
                        </a>
                        @guest
                            <p class="">Tendrás que iniciar sesión para completar la reserva.</p>
                        @endguest
                    @else
                        <button disabled class="">
                            No disponible
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>
</html>
