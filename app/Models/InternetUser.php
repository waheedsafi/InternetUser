<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternetUser extends Model
{
     protected $fillable = [
        'person_id', 'username', 'device_limit', 'mac_address', 'status'
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
