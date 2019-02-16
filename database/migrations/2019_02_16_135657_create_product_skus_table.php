<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title'); //SKU 名称
            $table->text('description'); //SKU 描述
            $table->decimal('price',10,2); //SKU价格
            $table->unsignedInteger('stock')->default(0); //库存
            $table->unsignedInteger('product_id'); //所属商品的id
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); 
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
        Schema::dropIfExists('product_skus');
    }
}
