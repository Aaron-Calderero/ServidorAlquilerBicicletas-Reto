<?php

namespace App\Http\Controllers;

use App\Models\Bike;

class BikeController extends Controller
{
    public function index()
    {
        $bikes = Bike::where('status', 'available')->get();

        return view('bikes.index', compact('bikes'));
    }

    public function show(Bike $bike)
    {
        return view('bikes.show', compact('bike'));
    }
}