<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomMember extends Model
{
    protected $fillable = [
        'room_id', 'contract_id', 'name', 'phone', 
        'id_card_number', 'id_card_front', 'id_card_back', 
        'dob', 'gender'
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    
    public function genderLabel(): string
    {
        return match($this->gender) {
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
            default => 'Chưa xác định',
        };
    }
}
