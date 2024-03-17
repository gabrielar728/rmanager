<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'row', 'article_id', 'group_id', 'status_id', 'worker_id', 'workers_nr', 'initial_production_date', 'production_date', 'production_date_week',  'finished_at', 'serial_no', 'sales_order', 'product', 'scanned_barcode',    ];

    public function article()
    {
        return $this->belongsTo(Article::class,'article_id');
    }

    public function dosages()
    {
        return $this->hasMany(Dosage::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class,'status_id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class,'worker_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class,'group_id');
    }
}
