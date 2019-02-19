<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InternalException;

class ProductSku extends Model
{
    protected $fillable = [
    	'title','description','stock','price'
    ];

    //与商品关联
    public function product()
    {
    	return $this->belongsTo(Product::class);
    }

    //加库存
    public function addStock($amount)
    {
    	if($amount < 0){
    		throw new InternalException("加库存不可小于0");    		
    	}

    	$this->increment('stock',$amount);
    }

    //减库存
    public function decreaseStock($amount)
    {
    	if($amount < 0){
    		throw new InternalException('减库存不可小于0');
    	}

    	return $this->newQuery()->where('id',$this->id)
    		->where('stock','>=',$amount)->decrement('stock',$amount);
    }
}
