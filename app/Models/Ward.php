<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code', 'name', 'type', 'district_code', 'province_code'];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }
}
