<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Catálogo - {{ config('app.name') }}</title>
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

        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold">Catálogo de bicicletas</h1>
                <p class="text-gray-600 mt-1">Elige tu bicicleta y resérvala en unos clics.</p>
            </div>
            @auth
                <a href="{{ route('bookings.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Mis reservas</a>
            @else
                <a href="{{ route('login') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Iniciar sesión para reservar</a>
            @endauth
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($bikes as $bike)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden flex flex-col">
                    @if ($bike->image)
                        <img src="{{ asset('storage/' . $bike->image) }}" alt="{{ $bike->name }}"
                             class="h-48 w-full object-cover">
                    @else
                        <div class="h-48 bg-gray-200 flex items-center justify-center text-5xl">🚲</div>
                    @endif

                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="text-lg font-semibold">{{ $bike->name }}</h2>
                            @if ($bike->available)
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Disponible</span>
                            @else
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">No disponible</span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 mt-1">{{ $bike->type }}</p>
                        <p class="text-sm text-gray-600 mt-2 flex-1">{{ \Illuminate\Support\Str::limit($bike->description, 90) }}</p>

                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xl font-bold text-indigo-600">
                                {{ number_format($bike->price_per_day, 2, ',', '.') }} €
                                <span class="text-sm font-normal text-gray-500">/ día</span>
                            </span>
                            <a href="{{ route('bikes.show', $bike) }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Ver detalles</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500 py-12">No hay bicicletas disponibles por ahora.</p>
            @endforelse
        </div>

        @if ($bikes instanceof \Illuminate\Pagination\AbstractPaginator && $bikes->hasPages())
            <div class="mt-8">
                {{ $bikes->links() }}
            </div>
        @endif

    </main>
</body>
</html>