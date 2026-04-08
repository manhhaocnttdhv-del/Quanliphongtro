<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id', 'name', 'price', 'area', 'floor', 'description', 
        'amenities', 'electricity_price', 'water_price', 'service_fee', 'status',
        'province_name', 'district_name', 'ward_name', 'address_detail',
        'latitude', 'longitude',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    protected $casts = [
        'price' => 'decimal:0',
        'electricity_price' => 'decimal:0',
        'water_price' => 'decimal:0',
        'service_fee' => 'decimal:0',
        'amenities' => 'array',
    ];

    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(RoomImage::class)->where('is_primary', true)
            ->orWhere(function ($q) {
                $q->orderBy('id');
            });
    }

    public function rentRequests()
    {
        return $this->hasMany(RentRequest::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function utilities()
    {
        return $this->hasMany(Utility::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function members()
    {
        return $this->hasMany(RoomMember::class);
    }

    public function activeContract()
    {
        return $this->hasOne(Contract::class)->where('status', 'active')->latest();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function statusLabel(): string
    {
        return $this->status === 'available' ? 'Còn trống' : 'Đã thuê';
    }

    public function statusBadge(): string
    {
        return $this->status === 'available' ? 'success' : 'danger';
    }
}
