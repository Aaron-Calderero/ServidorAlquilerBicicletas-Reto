<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $bike->model }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 antialiased">

    {{-- ===== BARRA DE NAVEGACIÓN ===== --}}
    @php
        $link = 'px-3 py-2 rounded-md text-sm font-medium transition';
        $on   = 'bg-indigo-100 text-indigo-700';
        $off  = 'text-gray-600 hover:bg-gray-100';
    @endphp
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('bikes.index') }}" class="text-lg font-bold text-indigo-600">
                🚲 {{ config('app.name') }}
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('bikes.index') }}"
                   class="{{ $link }} {{ request()->routeIs('bikes.*') ? $on : $off }}">Catálogo</a>

                @auth
                    <a href="{{ route('bookings.index') }}"
                       class="{{ $link }} {{ request()->routeIs('bookings.*') ? $on : $off }}">Mis reservas</a>
                    <a href="{{ route('profile.edit') }}"
                       class="{{ $link }} {{ $off }}">{{ Auth::user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="{{ $link }} text-red-600 hover:bg-red-50">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="{{ $link }} {{ $off }}">Iniciar sesión</a>
                    <a href="{{ route('register') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">

        {{-- Mensajes flash --}}
        @if (session('success'))
            <div class="mb-6 rounded-md bg-green-100 border border-green-300 text-green-800 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 rounded-md bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('bikes.index') }}" class="inline-block bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-50">&larr; Volver al catálogo</a>
            @auth
                <a href="{{ route('bookings.index') }}" class="inline-block bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-50">Mis reservas</a>
            @endauth
        </div>

        <div class="mt-4 bg-white rounded-lg shadow-sm overflow-hidden md:flex">
            <div class="md:w-1/2">
                <div class="h-72 md:h-full bg-gray-200 flex items-center justify-center text-7xl">🚲</div>
            </div>

            <div class="p-6 md:w-1/2 flex flex-col">
                <div class="flex items-start justify-between gap-2">
                    <h1 class="text-3xl font-bold">{{ $bike->model }}</h1>
                    @if ($bike->status === 'available')
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Disponible</span>
                    @else
                        <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full">No disponible</span>
                    @endif
                </div>

                <p class="text-gray-500 mt-1">Nº serie: {{ $bike->serial_number }}</p>
                <div class="flex-1"></div>

                <p class="mt-6 text-3xl font-bold text-indigo-600">
                    {{ number_format($bike->price_per_hour, 2, ',', '.') }} €
                    <span class="text-base font-normal text-gray-500">/ hora</span>
                </p>

                <div class="mt-6">
                    @if ($bike->status === 'available')
                        {{-- Si el visitante no ha iniciado sesión, el middleware 'auth' lo enviará al login --}}
                        <a href="{{ route('bookings.create', $bike) }}"
                           class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700">
                            Reservar esta bicicleta
                        </a>
                        @guest
                            <p class="text-sm text-gray-500 mt-2">Tendrás que iniciar sesión para completar la reserva.</p>
                        @endguest
                    @else
                        <button disabled class="bg-gray-300 text-gray-500 px-6 py-3 rounded-md cursor-not-allowed">
                            No disponible
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>
</html>
