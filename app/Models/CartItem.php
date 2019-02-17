<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['amount'];

    public $timestamps = false;

    //关联用户模型
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    //关联ProductSku模型
    public function productSku()
    {
    	return $this->belongsTo(ProductSku::class);
    }
}
