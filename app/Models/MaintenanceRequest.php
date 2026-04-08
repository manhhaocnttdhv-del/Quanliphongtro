<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'room_id', 'user_id', 'title', 'description', 
        'images', 'status', 'priority', 'admin_notes'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Chờ xử lý',
            'in_progress' => 'Đang xử lý',
            'resolved' => 'Đã giải quyết',
            'cancelled' => 'Đã hủy',
            default => $this->status,
        };
    }

    public function statusBadge(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'in_progress' => 'primary',
            'resolved' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }
}
