<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directorate extends Model
{
     public function directorateType()
    {
        return $this->belongsTo(DirectorateType::class);
    }

    public function persons()
    {
        return $this->hasMany(Person::class);
    }
}
