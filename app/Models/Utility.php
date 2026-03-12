<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'month', 'year',
        'electricity_old', 'electricity_new',
        'water_old', 'water_new',
        'electricity_price', 'water_price',
        'electricity_amount', 'water_amount',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function calcElectricity(): float
    {
        return ($this->electricity_new - $this->electricity_old) * $this->electricity_price;
    }

    public function calcWater(): float
    {
        return ($this->water_new - $this->water_old) * $this->water_price;
    }
}
