<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;

class PaymentController extends Controller
{
    //

    public function payByAlipay (Order $order,Request $request)
    {
    	// 判断订单是否属于当前用户
    	$this->authorize('own',$order);

    	// 订单已支付或者已关闭
    	if($order->paid_at || $order->closed){
    		throw new InvalidRequestException('订单状态不正确');
    	}

    	// 调用支付宝的网页支付
    	return app('alipay')->web([
            'out_trade_no' => $order->no, // 订单编号，需保证在商户端不重复
            'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后两位
            'subject'      => '支付 Laravel Shop 的订单：'.$order->no, // 订单标题
        ]);
    }
}
