<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
     protected $table = 'person';
     protected $fillable = [
        'name', 'lastname', 'email', 'phone', 'position', 'directorates_id', 'employment_id'
    ];

    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'directorates_id');
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class, 'employment_id');
    }

    public function internetUser()
    {
        return $this->hasOne(InternetUser::class);
    }
}
