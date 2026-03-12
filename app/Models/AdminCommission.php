<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id', 'invoice_id', 'amount', 'rate', 'status',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
