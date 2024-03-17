<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('row');
            $table->integer('article_id')->unsigned();
            $table->foreign('article_id')->references('id')->on('articles');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->integer('status_id')->unsigned()->default(1);
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->integer('worker_id')->unsigned()->default(1);
            $table->string('initial_production_date')->nullable();
            $table->date('production_date')->nullable();
            $table->foreign('worker_id')->references('id')->on('workers');
            $table->datetime('finished_at')->nullable();
            $table->integer('workers_nr')->unsigned();
            $table->string('production_date_week')->nullable();
            $table->string('serial_no')->unique()->nullable();
            $table->string('sales_order')->nullable();
            $table->string('product')->nullable();
            $table->boolean('scanned_barcode')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
