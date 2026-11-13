<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reservar bicicleta - {{ config('app.name') }}</title>
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
            <a href="{{ route('bikes.show', $bike) }}" class="inline-block bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-50">&larr; Volver a la bicicleta</a>
            <a href="{{ route('bikes.index') }}" class="inline-block bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-50">Catálogo</a>
        </div>

        <div class="mt-4 max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold mb-1">Reservar: {{ $bike->model }}</h1>
            <p class="text-gray-600 mb-6">
                Nº serie: {{ $bike->serial_number }} ·
                <span class="font-semibold text-indigo-600">{{ number_format($bike->price_per_hour, 2, ',', '.') }} € / hora</span>
            </p>

            <form method="POST" action="{{ route('bookings.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="bike_id" value="{{ $bike->id }}">

                <div>
                    <label for="start_time" class="block text-sm font-medium mb-1">Inicio de la reserva</label>
                    <input type="datetime-local" id="start_time" name="start_time"
                           value="{{ old('start_time') }}" min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('start_time')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium mb-1">Fin de la reserva</label>
                    <input type="datetime-local" id="end_time" name="end_time"
                           value="{{ old('end_time') }}" min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('end_time')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-sm text-gray-600">
                    Total estimado: <span id="estimate" class="font-semibold text-indigo-600">—</span>
                    <span class="text-gray-400">(se cobra por hora empezada)</span>
                </p>

                @error('bike_id')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('bikes.show', $bike) }}" class="inline-block bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-50">Cancelar</a>
                    <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                        Confirmar reserva
                    </button>
                </div>
            </form>
        </div>

    <script>
        const price = {{ (float) $bike->price_per_hour }};
        const startInput = document.getElementById('start_time');
        const endInput = document.getElementById('end_time');
        const estimate = document.getElementById('estimate');

        function updateEstimate() {
            const ms = new Date(endInput.value) - new Date(startInput.value);
            estimate.textContent = ms > 0
                ? (Math.ceil(ms / 3600000) * price).toFixed(2).replace('.', ',') + ' €'
                : '—';
        }
        startInput.addEventListener('input', updateEstimate);
        endInput.addEventListener('input', updateEstimate);
    </script>
    </main>
</body>
</html>
