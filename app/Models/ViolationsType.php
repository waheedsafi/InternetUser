<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationsType extends Model
{
    protected $fillable = [ 'name',];
    public function violation(){
        return $this->hasMany(Violation::class, 'violation_type_id');
    }

}
