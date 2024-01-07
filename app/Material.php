<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    public $timestamps = false;
    protected $table = 'materials';
    protected $fillable = [
        'name', 'unit', 
    ];

    public function articles_materials()
    {
        return $this->hasMany(ArticlesMaterial::class);
    }

    public function pumps()
    {
        return $this->hasMany(Pump::class);
    }

    public function dosages()
    {
        return $this->hasMany(Dosage::class);
    }
}
