<?php

namespace App\Services;

use Auth;
use App\Models\CartItem;

class CartService
{
	//购物车列表
	public function get()
	{
		return Auth::user()->cartItems()->with(['productSku.product'])->get();
	}

	//添加商品到购物车
	public function add($skuId,$amount)
	{
		$user = Auth::user();

		// 从数据库中查询该商品是否已经在购物车中
		$item = $user->cartItems()->where('product_sku_id',$skuId)->first();
		if($item){
			// 如果存在则直接叠加商品数量
			$item->update([
				'amount' => $item->amount + $amount
			]);
		}else{
			// 否则创建一个新的购物车记录
			$item = new CartItem(['amount' => $amount]);
			$item->user()->associate($user);
			$item->productSku()->associate($skuId);
			$item->save();
		}

		return $item;
	}

	//删除商品于购物车
	public function remove($skuIds)
	{
		// 可以传单个 ID，也可以传 ID 数组
		if(! is_array($skuIds)){
			$skuIds = [$skuIds];
		}

		Auth::user()->cartItems()->whereIn('product_sku_id',$skuIds)->delete();
	}
}