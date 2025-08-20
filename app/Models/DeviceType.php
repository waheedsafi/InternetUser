<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
     protected $guarded=[];
     
     public function internetUsers()
{
    return $this->belongsToMany(InternetUser::class, 'internet_user_devices', 'device_type_id', 'internet_user_id')->withTimestamps();
}

}
