<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $table = 'articles';
    protected $fillable = [
        'name','workers_required','status_id','category_id','client_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function article_category()
    {
        return $this->belongsTo(ArticleCategory::class,'category_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'articles_groups', 'group_id', 'article_id');
    }

    public function articles_materials()
    {
        return $this->hasMany(ArticlesMaterial::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
