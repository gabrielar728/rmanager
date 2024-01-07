<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $table = 'processes';
    protected $fillable = [
        'name',
    ];

    public function articles_materials()
    {
        return $this->hasMany(ArticlesMaterial::class);
    }
}
