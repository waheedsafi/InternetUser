<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
     use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
        
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',  
    ];

    
    // public function isAdmin()
    // {
    //     return $this->role === 'admin';
    // }

    // protected static function boot()
    // {
    //     parent::boot();

        
    //     static::creating(function ($user) {
    //         if (empty($user->role)) {
    //             $user->role = 'user'; 
    //         }
    //     });
    // }
}
