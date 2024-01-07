<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dosage extends Model
{
    protected $table = 'dosages';
    protected $fillable = [
        'quantity', 'product_id', 'pump_id', 'material_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function pump()
    {
        return $this->belongsTo(Pump::class,'pump_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class,'material_id');
    }

}
