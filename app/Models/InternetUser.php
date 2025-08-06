<?php

namespace App\Models;

use App\Enum\DeviceTypeEnum;
use Illuminate\Database\Eloquent\Model;

class InternetUser extends Model
{
     protected $fillable = [
        'person_id', 'username', 'device_limit', 'mac_address', 'status'
    ];
    protected $casts = [
    'device_type' => DeviceTypeEnum::class,
];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }
}
