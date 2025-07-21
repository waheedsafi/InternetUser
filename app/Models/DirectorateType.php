<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectorateType extends Model
{
     protected $fillable = ['name'];

    public function directorates()
    {
        return $this->hasMany(Directorate::class);
    }
}
