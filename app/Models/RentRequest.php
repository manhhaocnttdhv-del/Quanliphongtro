<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'room_id', 'note', 'move_in_date', 'agreed_rent', 'status', 'requested_at'];

    protected $casts = [
        'requested_at' => 'datetime',
        'move_in_date' => 'date',
        'agreed_rent'  => 'decimal:0',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'  => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            default    => 'Không xác định',
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'pending'  => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default    => 'secondary',
        };
    }
}
