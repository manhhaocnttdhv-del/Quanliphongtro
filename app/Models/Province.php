<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code', 'name', 'type'];

    public function districts()
    {
        return $this->hasMany(District::class, 'province_code', 'code');
    }
}
