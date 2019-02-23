<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;

class OrderService
{
	public function store(User $user,UserAddress $address,$remark,$items)
	{
		//开启一个数据库事务
		$order = \DB::transaction(function() use ($user,$address,$remark,$items){
			// 更新此地址的最后使用时间
			$address->update(['last_used_at' =>Carbon::now()]);

			//创建一个订单
			$order = new Order([
				'address' => [
					'address' => $address->full_address,
    				'zip' => $address->zip,
    				'contact_name' => $address->contact_name,
    				'contact_phone' => $address->contact_phone,
				],
				'remark' => $remark,
				'total_amount' => 0,
			]);

			// 订单关联到当前用户
			$order->user()->associate($user);
			//入库
			$order->save();

			$totalAmount = 0;
			// 遍历用户提交的 SKU
			foreach($items as $data){
				$sku = ProductSku::find($data['sku_id']);
				// 创建一个 OrderItem 并直接与当前订单关联
				$item = $order->items()->make([
					'amount' => $data['amount'],
					'price' => $sku->price,
				]);
				//订单项关联到商品
				$item->product()->associate($sku->product_id);
				//订单项关联到商品SKU
				$item->productSku()->associate($sku);
				//入库
    			$item->save();
    			$totalAmount += $sku->price * $data['amount']; 

    			//提交订单时判断订单项里,某商品SKU是否还有库存量
    			if($sku->decreaseStock($data['amount']) <= 0){
    				throw new InvalidRequestException('该商品库存不足');    				
    			}
			}

			//更新订单总金额
			$order->update(['total_amount' => $totalAmount]);

			//将下单商品从购物车中移除
    		$skuIds = collect($items)->pluck('sku_id')->all();

    		//通过 app() 函数创建封装的库类CartService 对象
    		app(CartService::class)->remove($skuIds);

    		return $order;
		});

		// 这里我们直接使用 dispatch 函数,触发这个在规定时间内未支付订单任务
		dispatch(new CloseOrder($order,config('app.order_ttl')));

		return $order;
	}
}