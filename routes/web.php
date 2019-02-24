<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', 'PagesController@root')->name('root');
Route::redirect('/','/products')->name('root');

Auth::routes(['verify' => true]);

Route::group(['middleware' => ['auth','verified']],function(){
	Route::get('user_addresses','UserAddressesController@index')->name('user_addresses.index');

	Route::get('user_addresses/create','UserAddressesController@create')->name('user_addresses.create');
	Route::post('user_addresses','UserAddressesController@store')->name('user_addresses.store');
	Route::get('user_addresses/{user_address}','UserAddressesController@edit')->name('user_addresses.edit');
	Route::put('user_addresses/{user_address}','UserAddressesController@update')->name('user_addresses.update');
	Route::delete('user_addresses/{user_address}','UserAddressesController@destroy')
	->name('user_addresses.destroy');

	//收藏商品
	Route::post('products/{product}/favorite','ProductsController@favor')->name('products.favor');
	//取消收藏
	Route::delete('product/{product}/favorite','ProductsController@disfavor')->name('products.disfavor');
	//收藏商品列表
	Route::get('products/favorites','ProductsController@favorites')->name('products.favorites');

	//添加商品到购物车
	Route::post('cart','CartController@add')->name('cart.add');
	//购物车列表
	Route::get('cart','CartController@index')->name('cart.index');
	//移除商品于购物车
	Route::delete('cart/{sku}','CartController@remove')->name('cart.remove');

	//下订单
	Route::post('orders','OrdersController@store')->name('orders.store');
	//订单列表
	Route::get('orders','OrdersController@index')->name('orders.index');
	//订单详情
	Route::get('orders/{order}','OrdersController@show')->name('orders.show');

	//订单的支付宝支付
	Route::get('payment/{order}/alipay','PaymentController@payByAlipay')->name('payment.alipay');

	//确认收货
	Route::post('orders/{order}/received','OrdersController@received')->name('orders.received');

	//展示评价
	Route::get('orders/{order}/review','OrdersController@review')->name('orders.review.show');
	//提交评价
	Route::post('orders/{order}/review','OrdersController@sendReview')->name('orders.review.store');
	//提交申请退款
	Route::post('orders/{order}/apply_refund','OrdersController@applyRefund')->name('orders.apply_refund');


});

Route::get('products','ProductsController@index')->name('products.index');
Route::get('products/{product}','ProductsController@show')->name('products.show');
