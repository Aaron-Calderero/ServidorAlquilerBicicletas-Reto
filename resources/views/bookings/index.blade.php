<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mis reservas - {{ config('app.name') }}</title>
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
            ];
            $statusLabels = [
                'pending'   => 'Pendiente',
                'confirmed' => 'Confirmada',
                'cancelled' => 'Cancelada',
            ];
        @endphp

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-3xl font-bold">Mis reservas</h1>
            <a href="{{ route('bikes.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">+ Nueva reserva (ver catálogo)</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Bicicleta</th>
                        <th class="px-4 py-3">Inicio</th>
                        <th class="px-4 py-3">Fin</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($bookings as $booking)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $booking->bike->name }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ number_format($booking->total_price, 2, ',', '.') }} €</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $statusStyles[$booking->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('bookings.show', $booking) }}"
                                   class="inline-block border border-indigo-300 text-indigo-700 px-3 py-1 rounded-md hover:bg-indigo-50">Ver</a>

                                @if (in_array($booking->status, ['pending', 'confirmed']))
                                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="inline"
                                          onsubmit="return confirm('¿Seguro que quieres cancelar esta reserva?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="border border-red-300 text-red-700 px-3 py-1 rounded-md hover:bg-red-50 ml-2">Cancelar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                Aún no tienes reservas.
                                <a href="{{ route('bikes.index') }}" class="text-indigo-600 hover:underline">Ver catálogo</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookings instanceof \Illuminate\Pagination\AbstractPaginator && $bookings->hasPages())
            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        @endif

    </main>
</body>
</html>
