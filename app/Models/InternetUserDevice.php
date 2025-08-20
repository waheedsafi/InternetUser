<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternetUserDevice extends Model
{
     protected $fillable =['internet_user_id','device_type_id'];

///////////////////////////////
      public function internetUser()
    {
        return $this->belongsTo(InternetUser::class);
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class);
    }
}
