<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleGroup extends Model
{
    protected $table = 'articles_groups';
    protected $fillable = [
        'row', 'article_id', 'group_id',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class,'group_id');
    }
}
