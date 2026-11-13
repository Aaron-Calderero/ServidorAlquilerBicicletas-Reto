<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalle de la reserva - {{ config('app.name') }}</title>
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

        @php
            $statusStyles = [
                'pending'   => 'bg-yellow-100 text-yellow-800',
                'confirmed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
                'completed' => 'bg-gray-200 text-gray-700',
                'active'    => 'bg-blue-100 text-blue-800',
            ];
            $statusLabels = [
                'pending'   => 'Pendiente',
                'confirmed' => 'Confirmada',
                'cancelled' => 'Cancelada',
                'completed' => 'Finalizada',
                'active'    => 'Activa',
            ];
        @endphp

        @php
            $start = \Carbon\Carbon::parse($booking->start_time);
            $end   = \Carbon\Carbon::parse($booking->end_time);
        @endphp

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('bookings.index') }}" class="inline-block bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-50">&larr; Volver a mis reservas</a>
            <a href="{{ route('bikes.index') }}" class="inline-block bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-50">Catálogo</a>
            <a href="{{ route('bikes.show', $booking->bike) }}" class="inline-block bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-50">Ver bicicleta</a>
        </div>

        <div class="mt-4 max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-start justify-between">
                <h1 class="text-2xl font-bold">Reserva #{{ $booking->id }}</h1>
                <span class="px-3 py-1 rounded-full text-sm {{ $statusStyles[$booking->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                </span>
            </div>

            <dl class="mt-6 divide-y divide-gray-100 text-sm">
                <div class="py-3 flex justify-between">
                    <dt class="text-gray-500">Bicicleta</dt>
                    <dd class="font-medium">{{ $booking->bike->model }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="text-gray-500">Nº de serie</dt>
                    <dd>{{ $booking->bike->serial_number }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="text-gray-500">Inicio</dt>
                    <dd>{{ $start->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="text-gray-500">Fin</dt>
                    <dd>{{ $end->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="text-gray-500">Horas</dt>
                    <dd>{{ (int) ceil($start->diffInMinutes($end) / 60) }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="text-gray-500">Total</dt>
                    <dd class="text-lg font-bold text-indigo-600">{{ number_format($booking->total_price, 2, ',', '.') }} €</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="text-gray-500">Reservado el</dt>
                    <dd>{{ $booking->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>

            @if (!in_array($booking->status, ['cancelled', 'completed']))
                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-6 text-right"
                      onsubmit="return confirm('¿Seguro que quieres cancelar esta reserva?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">
                        Cancelar reserva
                    </button>
                </form>
            @endif
        </div>

    </main>
</body>
</html>
