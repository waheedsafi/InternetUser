<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\InternetUser;

class AccountActivation extends Model
{
    protected $fillable = [
        'internet_user_id',
        'reason',
        'activated_by_user_id',
    ];

    
    public function internetUser()
    {
        return $this->belongsTo(InternetUser::class);
    }

    public function activatedBy()
    {
        return $this->belongsTo(User::class, 'activated_by_user_id');
    }
}
