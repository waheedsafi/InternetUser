<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountActivation extends Model
{
    protected $fillable = [
        'internet_user_id',
        'reason',
    ];

    
    public function internetUser()
    {
        return $this->belongsTo(InternetUser::class);
    }
}
