<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalle de la reserva - {{ config('app.name') }}</title>
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

        <div class="">
            <a href="{{ route('bookings.index') }}" class="">&larr; Volver a mis reservas</a>
            <a href="{{ route('bikes.index') }}" class="">Catálogo</a>
            <a href="{{ route('bikes.show', $booking->bike) }}" class="">Ver bicicleta</a>
        </div>

        <div class="">
            <div class="">
                <h1 class="">Reserva #{{ $booking->id }}</h1>
                <span class="">
                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                </span>
            </div>

            <dl class="">
                <div class="">
                    <dt class="">Bicicleta</dt>
                    <dd class="">{{ $booking->bike->model }}</dd>
                </div>
                <div class="">
                    <dt class="">Nº de serie</dt>
                    <dd>{{ $booking->bike->serial_number }}</dd>
                </div>
                <div class="">
                    <dt class="">Inicio</dt>
                    <dd>{{ $start->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="">
                    <dt class="">Fin</dt>
                    <dd>{{ $end->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="">
                    <dt class="">Horas</dt>
                    <dd>{{ (int) ceil($start->diffInMinutes($end) / 60) }}</dd>
                </div>
                <div class="">
                    <dt class="">Total</dt>
                    <dd class="">{{ number_format($booking->total_price, 2, ',', '.') }} €</dd>
                </div>
                <div class="">
                    <dt class="">Reservado el</dt>
                    <dd>{{ $booking->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>

            @if (!in_array($booking->status, ['cancelled', 'completed']))
                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class=""
                      onsubmit="return confirm('¿Seguro que quieres cancelar esta reserva?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="">
                        Cancelar reserva
                    </button>
                </form>
            @endif
        </div>

    </main>
</body>
</html>
