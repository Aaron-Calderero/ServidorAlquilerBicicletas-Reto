<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Catálogo - {{ config('app.name') }}</title>
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
            <div>
                <h1 class="">Catálogo de bicicletas</h1>
                <p class="">Elige tu bicicleta y resérvala en unos clics.</p>
            </div>
            
        </div>

        <div class="">
            @forelse ($bikes as $bike)
                <div class="">
                    <div class="">🚲</div>

                    <div class="">
                        <div class="">
                            <h2 class="">{{ $bike->model }}</h2>
                            @if ($bike->status === 'available')
                                <span class="">Disponible</span>
                            @else
                                <span class="">No disponible</span>
                            @endif
                        </div>

                        <p class="">Nº serie: {{ $bike->serial_number }}</p>
                        <div class=""></div>

                        <div class="">
                            <span class="">
                                {{ number_format($bike->price_per_hour, 2, ',', '.') }} €
                                <span class="">/ hora</span>
                            </span>
                            <a href="{{ route('bikes.show', $bike) }}" class="">Ver detalles</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="">No hay bicicletas disponibles por ahora.</p>
            @endforelse
        </div>

        @if ($bikes instanceof \Illuminate\Pagination\AbstractPaginator && $bikes->hasPages())
            <div class="">
                {{ $bikes->links() }}
            </div>
        @endif

    </main>
</body>
</html>
