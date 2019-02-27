<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\UserAddress;
/*
use App\Models\ProductSku;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Services\CartService;
*/
use App\Services\OrderService;
use App\Http\Requests\SendReviewRequest;
use Carbon\Carbon;
use App\Events\OrderReviewed;
use App\Http\Requests\ApplyRefundRequest;

class OrdersController extends Controller
{
    //创建订单
    public function store(OrderRequest $request,OrderService $orderService)
    {

    	$user = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        $remark = $request->input('remark');
        $items = $request->input('items');

        return $orderService->store($user,$address,$remark,$items);
    /*
    	//开启一个数据库事务
    	$order = \DB::transaction(function() use ($user,$request,$cartService){
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
    				throw new InvalidRequestException('该商品库存不足');
    			}
    		}	

    		//更新订单总金额
    		$order->update(['total_amount' => $totalAmount]);

    		//将下单商品从购物车中移除
    		$skuIds = collect($items)->pluck('sku_id')->all();
    		//$user->cartItems()->whereIn('product_sku_id',$skuIds)->delete();
            $cartService->remove($skuIds);

    		return $order;
    	});

    	//触发这个在规定时间内未支付订单任务
    	$this->dispatch(new CloseOrder($order,config('app.order_ttl')));

    	return $order;
    */
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

    //确认收货
    public function received(Order $order,Request $request)
    {
        //校验权限
        $this->authorize('own',$order);

        // 判断订单的发货状态是否为已发货
        if($order->ship_status !== Order::SHIP_STATUS_DELIVERED){
            throw new InvalidRequestException('发货状态不正确');
        }

        // 更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        // 返回原页面
        //return redirect()->back();

        //由于我们把确认收货的操作从表单提交改成了 AJAX 请求,返回值修改如下
        // 返回订单信息
        return $order;
    }

    //展示评价
    public function review(Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);

        // 判断是否已经支付
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未支付，不可评价');
        }

        // 使用 load 方法加载关联数据，避免 N + 1 性能问题
        $order = $order->load(['items.productSku','items.product']);
        //echo '<pre>';print_r($order->toArray());exit;

        return view('orders.review',['order' => $order]);
    }

    //提交发布评价
    public function sendReview(Order $order,SendReviewRequest $request)
    {
        // 校验权限
        $this->authorize('own', $order);

        // 判断是否已经支付
        if(!$order->paid_at){
            throw new InvalidRequestException("该订单未支付，不可评价");           
        }

        // 判断是否已经评价
        if($order->reviewed){
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }

        $reviews = $request->input('reviews');
        //echo '<pre>';print_r($reviews);exit;
        // 开启事务
        \DB::transaction(function() use($reviews,$order){
            // 遍历用户提交的数据
            foreach($reviews as $review){
                $orderItem = $order->items()->find($review['id']);
                // 保存评分和评价
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);

            }

            // 将订单标记为已评价
            $order->update(['reviewed' => true]);

            // 触发事件
            event(new OrderReviewed($order));
        });

        return redirect()->back();
    }
    
    //提交退款申请
    public function applyRefund(Order $order,ApplyRefundRequest $request)
    {
        // 校验订单是否属于当前用户
        $this->authorize('own',$order);

        // 判断订单是否已付款
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未支付，不可退款');
        }

        // 判断订单退款状态是否正确
        if($order->refund_status !== Order::REFUND_STATUS_PENDING){
            throw new InvalidRequestException('该订单已经申请过退款，请勿重复申请');
        }

        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra = $order->extra ?: [];
        $extra['refund_reason'] = $request->input('reason');

        // 将订单退款状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra,
        ]);

        return $order;

    }

    
}
