<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pump extends Model
{
    protected $table = 'pumps';
    protected $fillable = [
        'name', 'location', 'ip', 'port', 'material_id','ratio',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class,'material_id');
    }

    public function dosages()
    {
        return $this->hasMany(Dosage::class);
    }
}
