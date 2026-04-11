<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'user_id',
        'tenant_name', 'tenant_phone', 'tenant_cccd', 'num_people', 'note',
        'deposit_amount', 'move_in_date',
        'status', 'expired_at',
        'payment_method', 'payment_ref', 'paid_at', 'confirmed_by',
        'contract_id',
    ];

    protected $casts = [
        'deposit_amount' => 'decimal:0',
        'move_in_date'   => 'date',
        'expired_at'     => 'datetime',
        'paid_at'        => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    // ─── Status Helpers ───────────────────────────────────

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'   => 'Chờ thanh toán',
            'paid'      => 'Đã đặt cọc',
            'cancelled' => 'Đã hủy',
            'converted' => 'Đã tạo hợp đồng',
            default     => 'Không xác định',
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'pending'   => 'warning',
            'paid'      => 'success',
            'cancelled' => 'danger',
            'converted' => 'info',
            default     => 'secondary',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'   => '#f59e0b',
            'paid'      => '#10b981',
            'cancelled' => '#ef4444',
            'converted' => '#3b82f6',
            default     => '#94a3b8',
        };
    }

    public function isExpired(): bool
    {
        return $this->status === 'pending'
            && $this->expired_at
            && $this->expired_at->isPast();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function paymentMethodLabel(): string
    {
        return $this->payment_method === 'online' ? 'Trực tuyến' : 'Tại chỗ (Offline)';
    }
}
