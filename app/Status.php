<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'statuses';
    protected $fillable = [
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
