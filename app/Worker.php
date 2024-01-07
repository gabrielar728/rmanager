<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Worker extends Authenticatable
{
    use Notifiable;

    protected $guard = 'worker';

    protected $table = 'workers';
    protected $fillable = [
        'last', 'first', 'card', 'status', 'password',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
