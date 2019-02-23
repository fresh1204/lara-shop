<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Models\ProductSku;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;

class OrdersController extends Controller
{
    //创建订单
    public function store(OrderRequest $request)
    {
    	$user = $request->user();
    	//开启一个数据库事务
    	$order = \DB::transaction(function() use ($user,$request){
    		$address = UserAddress::find($request->input('address_id'));
    		//更新此地址的最后使用时间
    		$address->update([
    			'last_used_at' => Carbon::now(),
    		]);

    		//创建一个订单
    		$order = new Order([
    			'address' => [
    				'address' => $address->full_address,
    				'zip' => $address->zip,
    				'contact_name' => $address->contact_name,
    				'contact_phone' => $address->contact_phone,
    			],
    			'remark' => $request->input('remark'),
    			'total_amount' => 0,
    		]);

    		//订单关联到当前用户
    		$order->user()->associate($user);
    		//入库
    		$order->save();
    		
    		$totalAmount = 0;
    		$items = $request->input('items');
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

    			if($sku->decreaseStock($data['amount']) <= 0){
    				throw new InvalidRequestException('InvalidRequestException');
    			}
    		}	

    		//更新订单总金额
    		$order->update(['total_amount' => $totalAmount]);

    		//将下单商品从购物车中移除
    		$skuIds = collect($items)->pluck('sku_id');
    		$user->cartItems()->whereIn('product_sku_id',$skuIds)->delete();

    		return $order;
    	});

    	//触发这个在规定时间内未支付订单任务
    	$this->dispatch(new CloseOrder($order,config('app.order_ttl')));

    	return $order;
    }

    //订单列表
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['items.product','items.productSku'])
            ->where('user_id',$request->user()->id)
            ->orderBy('created_at','desc')
            ->paginate();

        return view('orders.index',['orders' => $orders]);
    }

    //订单详情
    public function show(Order $order,Request $request)
    {
        //权限校验，只允许订单的创建者可以看到对应的订单信息
        $this->authorize('own',$order);

        //延迟预加载,在已经查询出来的模型上调用
        $order = $order->load('items.product','items.productSku');

        return view('orders.show',['order' => $order]);
    }
}
