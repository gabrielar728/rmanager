<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public $timestamps = false;

    protected $table = 'groups';
    protected $fillable = [
        'name',
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'articles_groups', 'group_id', 'article_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
