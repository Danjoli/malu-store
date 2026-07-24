<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory;

    public const ROLES = [
        'suporte' => 'Suporte',
        'admin' => 'Administrador',
        'superadmin' => 'Super Administrador',
    ];

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'role'
    ];

    protected $hidden = [
        'password'
    ];
}
