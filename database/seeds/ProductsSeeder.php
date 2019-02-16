<?php

use Illuminate\Database\Seeder;

//批量创建商品以及对应的SKU
class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //创建30个商品
        $products = factory(\App\Models\Product::class,30)->create();
        foreach ($products as $product) {
        	
        	//创建3个SKU,并且每个SKU的'product_id'字段都设为当前循环的商品id
        	$skus = factory(\App\Models\ProductSku::class,3)->create(['product_id' => $product->id]);

        	//找出价格最低的 SKU 价格，把商品价格设为改价格
        	$product->update(['price' => $skus->min('price')]);
        }
    }
}
