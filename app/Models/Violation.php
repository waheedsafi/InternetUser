<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
protected $guarded = [];
    public function internetUser()
    {
        return $this->belongsTo(InternetUser::class);
    }
    public function violationType(){
        return $this->belongsTo(ViolationsType::class,'violation_type_id');
    }
}
