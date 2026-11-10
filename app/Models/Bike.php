<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bike extends Model
{
    protected $fillable = ['serial_number', 'model', 'price_per_hour', 'status'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'bookings')
            ->withPivot('start_time', 'end_time', 'status', 'total_cost')
            ->withTimestamps();
    }
}
