<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mis reservas - {{ config('app.name') }}</title>
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

        <div class="">
            <h1 class="">Mis reservas</h1>
            <a href="{{ route('bikes.index') }}" class="">+ Nueva reserva (ver catálogo)</a>
        </div>

        <div class="">
            <table class="">
                <thead class="">
                    <tr>
                        <th class="">Bicicleta</th>
                        <th class="">Inicio</th>
                        <th class="">Fin</th>
                        <th class="">Total</th>
                        <th class="">Estado</th>
                        <th class="">Acciones</th>
                    </tr>
                </thead>
                <tbody class="">
                    @forelse ($bookings as $booking)
                        <tr>
                            <td class="">{{ $booking->bike->model }}</td>
                            <td class="">{{ \Carbon\Carbon::parse($booking->start_time)->format('d/m/Y H:i') }}</td>
                            <td class="">{{ \Carbon\Carbon::parse($booking->end_time)->format('d/m/Y H:i') }}</td>
                            <td class="">{{ number_format($booking->total_price, 2, ',', '.') }} €</td>
                            <td class="">
                                <span class="">
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </td>
                            <td class="">
                                <a href="{{ route('bookings.show', $booking) }}"
                                   class="">Ver</a>

                                @if (!in_array($booking->status, ['cancelled', 'completed']))
                                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class=""
                                          onsubmit="return confirm('¿Seguro que quieres cancelar esta reserva?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="">Cancelar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="">
                                Aún no tienes reservas.
                                <a href="{{ route('bikes.index') }}" class="">Ver catálogo</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookings instanceof \Illuminate\Pagination\AbstractPaginator && $bookings->hasPages())
            <div class="">
                {{ $bookings->links() }}
            </div>
        @endif

    </main>
</body>
</html>
