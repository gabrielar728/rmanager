<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticlesMaterial extends Model
{
    protected $table = 'articles_materials';
    protected $fillable = [
        'row', 'extra', 'article_id', 'quantity', 'material_id', 'process_id',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }
}
