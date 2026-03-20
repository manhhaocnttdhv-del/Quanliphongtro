<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'user_id', 'title', 'description',
        'images', 'priority', 'status', 'admin_note', 'resolved_at',
    ];

    protected $casts = [
        'images'      => 'array',
        'resolved_at' => 'datetime',
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
        return match ($this->status) {
            'pending'     => 'Chờ xử lý',
            'in_progress' => 'Đang xử lý',
            'done'        => 'Hoàn thành',
            'rejected'    => 'Từ chối',
            default       => 'Không xác định',
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'pending'     => 'warning',
            'in_progress' => 'info',
            'done'        => 'success',
            'rejected'    => 'danger',
            default       => 'secondary',
        };
    }

    public function priorityLabel(): string
    {
        return match ($this->priority) {
            'low'    => 'Thấp',
            'medium' => 'Trung bình',
            'high'   => 'Cao',
            'urgent' => 'Khẩn cấp',
            default  => 'Không xác định',
        };
    }

    public function priorityBadge(): string
    {
        return match ($this->priority) {
            'low'    => 'secondary',
            'medium' => 'primary',
            'high'   => 'warning',
            'urgent' => 'danger',
            default  => 'secondary',
        };
    }
}
