<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
        'id_card', 'dob', 'gender', 'province_name', 'district_name',
        'ward_name', 'address_detail',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'dob' => 'date',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'landlord', 'staff']);
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isLandlord(): bool
    {
        return $this->role === 'landlord';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    public function rentRequests()
    {
        return $this->hasMany(RentRequest::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Contract::class, 'user_id', 'contract_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'landlord_id');
    }

    public function commissions()
    {
        return $this->hasMany(AdminCommission::class, 'landlord_id');
    }
}
