<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reservar bicicleta - {{ config('app.name') }}</title>
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
            <a href="{{ route('bikes.show', $bike) }}" class="">&larr; Volver a la bicicleta</a>
            <a href="{{ route('bikes.index') }}" class="">Catálogo</a>
        </div>

        <div class="">
            <h1 class="">Reservar: {{ $bike->model }}</h1>
            <p class="">
                Nº serie: {{ $bike->serial_number }} ·
                <span class="">{{ number_format($bike->price_per_hour, 2, ',', '.') }} € / hora</span>
            </p>

            <form method="POST" action="{{ route('bookings.store') }}" class="">
                @csrf
                <input type="hidden" name="bike_id" value="{{ $bike->id }}">

                <div>
                    <label for="start_time" class="">Inicio de la reserva</label>
                    <input type="datetime-local" id="start_time" name="start_time"
                           value="{{ old('start_time') }}" min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="" required>
                    @error('start_time')
                        <p class="">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="">Fin de la reserva</label>
                    <input type="datetime-local" id="end_time" name="end_time"
                           value="{{ old('end_time') }}" min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="" required>
                    @error('end_time')
                        <p class="">{{ $message }}</p>
                    @enderror
                </div>

                <p class="">
                    Total estimado: <span id="estimate" class="">—</span>
                    <span class="">(se cobra por hora empezada)</span>
                </p>

                @error('bike_id')
                    <p class="">{{ $message }}</p>
                @enderror

                <div class="">
                    <a href="{{ route('bikes.show', $bike) }}" class="">Cancelar</a>
                    <button type="submit"
                            class="">
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
