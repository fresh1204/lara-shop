<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;

class CartController extends Controller
{
	//购物车列表,收货地址列表
	public function index(Request $request)
	{
		$cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();
		$addresses = $request->user()->addresses()->orderBy('last_used_at','desc')->get();

		return view('cart.index',['cartItems' => $cartItems,'addresses' => $addresses]);
	}

    //添加商品到购物车
    public function add(AddCartRequest $request)
    {
    	$user = $request->user();

    	$skuId = $request->input('sku_id');

    	$amount = $request->input('amount');

    	// 从数据库中查询该商品是否已经在购物车中
    	if($cart = $user->cartItems()->where('product_sku_id',$skuId)->first()){

    		//如果存在，则叠加商品数量
    		$cart->update([
    			'amount' => $cart->amount + $amount,
    		]);
    	}else{

    		//否则，创建一个新的购物车记录
    		$cart = new CartItem(['amount' => $amount]);
    		$cart->user()->associate($user);
    		$cart->productSku()->associate($skuId);
    		$cart->save();
    	}

    	return [];
    }

    //把商品从购物车移除
    public function remove(ProductSku $sku,Request $request)
    {
    	$request->user()->cartItems()->where('product_sku_id',$sku->id)->delete();

    	return [];
    }
}
