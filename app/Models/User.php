<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';  
    protected $primaryKey = 'google_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'google_id',
        'email',
        'name',
        'avatar',
        'permissions',
        'registration_time',
    ];

    protected $casts = [
        'permissions' => 'array',
        'registration_time' => 'datetime',
    ];
    
    public function hasRole($role)
    {
        return isset($this->permissions['role']) && strtolower($this->permissions['role']) === strtolower($role);
    }
}
