<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('bike')->where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('bookings.index', compact('bookings'));
    }

    public function create(Bike $bike)
    {
        if ($bike->status !== 'available') {
            return redirect()->route('bikes.index')->with('error', 'Esta bicicleta no se encuentra disponible para alquilar.');
        }
        return view('bookings.create', compact('bike'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bike_id' => 'required|exists:bikes,id',
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        $bike = Bike::findOrFail($request->bike_id);

        if ($bike->status !== 'available') {
            return back()->withErrors(['bike_id' => 'La bicicleta seleccionada no esta disponible.']);
        }

        $startTime=Carbon::parse($request->start_time);
        $endTime=Carbon::parse($request->end_time);

        $hasOverlap = Booking::where('bike_id', $bike->id)
            ->whereIn('status', ['pending', 'active'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time',[$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($q)use($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })
            ->exists();

        if ($hasOverlap) {
            return back()->withInput()->withErrors(['start_time' => 'La bicicleta ya esta reservada en ese horario.']);
        }

        $hours = $startTime->diffInHours($endTime);
        $hours = $hours > 0 ? $hours : 1;
        $totalCost = $hours * $bike->price_per_hour;

        Booking::create([
            'user_id' => Auth::id(),
            'bike_id' => $bike->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'pending',
            'total_cost' => $totalCost,
        ]);

        return redirect()->route('bookings.index')->with('success', 'Reserva realizada con exito!');
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta reserva.');
        }
        $booking->load('bike');
        return view('bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar esta reserva.');
        }
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Solo es posible cancelar reservas en estado pendiente.');
        }
        $booking->update(['status' => 'cancelled']);
        return redirect()->route('bookings.index')->with('success', 'La reserva ha sido cancelada correctamente.');
    }
}

