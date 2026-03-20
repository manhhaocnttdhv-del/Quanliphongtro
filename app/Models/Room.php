<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Approval statuses
    const APPROVAL_PENDING  = 'pending';
    const APPROVAL_APPROVED = 'approved';
    const APPROVAL_REJECTED = 'rejected';

    protected $fillable = [
        'landlord_id', 'name', 'price', 'area', 'floor', 'description', 
        'amenities', 'electricity_price', 'water_price', 'service_fee', 'status',
        'approval_status',
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
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(RoomReview::class)->latest();
    }

    public function averageRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
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

    public function hasLocation(): bool
    {
        return !empty($this->latitude) && !empty($this->longitude);
    }

    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->address_detail,
            $this->ward_name,
            $this->district_name,
            $this->province_name,
        ]);
        return implode(', ', $parts);
    }

    // ─── Approval Helpers ────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED);
    }

    public function isApproved(): bool
    {
        return $this->approval_status === self::APPROVAL_APPROVED;
    }

    public function approvalLabel(): string
    {
        return match ($this->approval_status) {
            self::APPROVAL_PENDING  => 'Chờ duyệt',
            self::APPROVAL_APPROVED => 'Đã duyệt',
            self::APPROVAL_REJECTED => 'Bị từ chối',
            default                 => 'N/A',
        };
    }

    public function approvalBadge(): string
    {
        return match ($this->approval_status) {
            self::APPROVAL_PENDING  => 'warning',
            self::APPROVAL_APPROVED => 'success',
            self::APPROVAL_REJECTED => 'danger',
            default                 => 'secondary',
        };
    }
}
