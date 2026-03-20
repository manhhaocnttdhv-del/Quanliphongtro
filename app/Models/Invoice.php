<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    // Statuses
    const STATUS_UNPAID   = 'unpaid';
    const STATUS_PAYING   = 'paying';
    const STATUS_PAID     = 'paid';
    const STATUS_OVERDUE  = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'room_id', 'contract_id', 'month', 'year',
        'room_fee', 'electricity_fee', 'water_fee', 'service_fee', 'total_amount',
        'status', 'payment_method', 'payment_ref', 'transaction_id', 'paid_at',
        'notes', 'due_date',
    ];

    protected $casts = [
        'paid_at'  => 'datetime',
        'due_date' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    // ─── Status Helpers ──────────────────────────────────────

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_UNPAID    => 'Chưa thanh toán',
            self::STATUS_PAYING    => 'Đang thanh toán',
            self::STATUS_PAID      => 'Đã thanh toán',
            self::STATUS_OVERDUE   => 'Quá hạn',
            self::STATUS_CANCELLED => 'Đã hủy',
            default                => 'Không xác định',
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            self::STATUS_UNPAID    => 'warning',
            self::STATUS_PAYING    => 'info',
            self::STATUS_PAID      => 'success',
            self::STATUS_OVERDUE   => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            default                => 'light',
        };
    }

    public function isOverdue(): bool
    {
        return $this->status !== self::STATUS_PAID
            && $this->status !== self::STATUS_CANCELLED
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Kiểm tra trùng thanh toán
     */
    public static function hasDuplicateTransaction(string $transactionId): bool
    {
        return self::where('transaction_id', $transactionId)
            ->where('status', self::STATUS_PAID)
            ->exists();
    }

    /**
     * VietQR URL
     */
    public function vietQrUrl(): string
    {
        $bankId     = Setting::get('vietqr_bank_id', 'MB');
        $accountNo  = Setting::get('vietqr_account_no', '');
        $amount     = (int) $this->total_amount;
        $addInfo    = urlencode('Thanh toan hoa don phong ' . ($this->room->name ?? '') . ' thang ' . $this->month . '/' . $this->year);

        return "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo={$addInfo}&accountName=" . urlencode(Setting::get('site_name', 'Nha Tro'));
    }

    /**
     * MoMo deep link
     */
    public function momoUrl(): string
    {
        $momoNumber = Setting::get('momo_number', '');
        $amount     = (int) $this->total_amount;
        $note       = urlencode('Thanh toan phong ' . ($this->room->name ?? '') . ' thang ' . $this->month . '/' . $this->year);

        return "https://nhantien.momo.vn/{$momoNumber}?amount={$amount}&note={$note}";
    }
}
