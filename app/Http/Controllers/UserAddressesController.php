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
}
