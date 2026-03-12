<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'contract_id', 'month', 'year',
        'room_fee', 'electricity_fee', 'water_fee', 'service_fee', 'total_amount',
        'status', 'payment_method', 'payment_ref', 'paid_at', 'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function statusLabel(): string
    {
        return $this->status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
    }

    public function statusBadge(): string
    {
        return $this->status === 'paid' ? 'success' : 'warning';
    }

    /**
     * Get VietQR URL for this invoice
     */
    public function vietQrUrl(): string
    {
        $bankId     = \App\Models\Setting::get('vietqr_bank_id', 'MB');
        $accountNo  = \App\Models\Setting::get('vietqr_account_no', '');
        $amount     = (int) $this->total_amount;
        $addInfo    = urlencode('Thanh toan hoa don phong ' . ($this->room->name ?? '') . ' thang ' . $this->month . '/' . $this->year);

        return "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo={$addInfo}&accountName=" . urlencode(\App\Models\Setting::get('site_name', 'Nha Tro'));
    }

    /**
     * Get MoMo deep link for this invoice
     */
    public function momoUrl(): string
    {
        $momoNumber = \App\Models\Setting::get('momo_number', '');
        $amount     = (int) $this->total_amount;
        $note       = urlencode('Thanh toan phong ' . ($this->room->name ?? '') . ' thang ' . $this->month . '/' . $this->year);

        return "https://nhantien.momo.vn/{$momoNumber}?amount={$amount}&note={$note}";
    }
}
