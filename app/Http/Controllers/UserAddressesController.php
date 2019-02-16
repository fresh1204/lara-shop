<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Http\Requests\UserAddressRequest;
class UserAddressesController extends Controller
{
    //用户收货地址首页
    public function index(Request $request)
    {
    	return view('user_addresses.index',['addresses' => $request->user()->addresses]);
    }

    //新增收货地址表单
    public function create()
    {
    	return view('user_addresses.create_and_edit',['address' => new UserAddress()]);
    }

    //对新增收货地址表单提交数据进行保存处理
    public function store(UserAddressRequest $request)
    {
    	$request->user()->addresses()->create($request->only([
    		'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
    	]));

    	return redirect()->route('user_addresses.index');
    }

    //修改收货地址表单
    public function edit(UserAddress $user_address)
    {
    	/*
		authorize('own', $user_address) 方法会获取第二个参数 $user_address 的类名: App\Models\UserAddress，
		然后在 AuthServiceProvider 类的 $policies 属性中寻找对应的策略，在这里就是 App\Policies\UserAddressPolicy，
		找到之后会实例化这个策略类，再调用名为 own() 方法，
		如果 own() 方法返回 false 则会抛出一个未授权的异常。
    	*/
    	$this->authorize('own',$user_address);

    	return view('user_addresses.create_and_edit',['address' => $user_address]);
    }

    //修改表单处理
    public function update(UserAddress $user_address,UserAddressRequest $request)
    {
    	$this->authorize('own',$user_address);

    	$user_address->update($request->only([
    		'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
    	]));

    	return redirect()->route('user_addresses.index');
    }

    //删除收货地址
    public function destroy(UserAddress $user_address)
    {
    	$user_address->delete();

    	//return redirect()->route('user_addresses.index');
    	return [];
    }
}
