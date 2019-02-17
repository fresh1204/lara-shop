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

});

Route::get('products','ProductsController@index')->name('products.index');
Route::get('products/{product}','ProductsController@show')->name('products.show');
