<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\InternalException;
use App\Models\OrderItem;

class ProductsController extends Controller
{
    //商品首页
    public function index(Request $request)
    {
    	
    	//$products = Product::query()->where('on_sale',true)->paginate(16);
    	// 创建一个查询构造器
    	$builder = Product::query()->where('on_sale',true);
    	//判断是否有提交search 参数，如果有，就赋值给变量$search
    	// search 参数用来模糊搜索商品
    	if($search = $request->input('search','')){
    		$like = '%'.$search.'%';
    		// 模糊搜索商品标题、商品详情、SKU 标题、SKU描述
    		$builder->where(function($query) use($like){
    			$query->where('title','like',$like)
    				->orWhere('description','like',$like)
    				->orWhereHas('skus',function($query) use ($like){
    					$query->where('title','like',$like)
    						->orWhere('description','like',$like);
    				});
    		});
    	}

    	// 是否有提交 order 参数，如果有就赋值给 $order 变量
        // order 参数用来控制商品的排序规则
    	if($order = $request->input('order','')){
    		// 是否是以 _asc 或者 _desc 结尾
    		if(preg_match('/^(.+)_(asc|desc)$/', $order,$m)){
    			// 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
    			if(in_array($m[1],['price','sold_count','rating'])){
    				// 根据传入的排序值来构造排序参数
    				$builder->orderBy($m[1],$m[2]);
    			}
    		}
    	}

    	$products = $builder->paginate(16);

    	return view('products.index',[
    		'products' => $products,
    		'filters' => [
    			'search' => $search,
    			'order' => $order,
    		]
    	]);
    }

    //商品详情
    public function show(Product $product,Request $request)
    {
    	//判断商品是否已经上架，如果没上架，则抛出异常
    	if(! $product->on_sale){
    		//throw new \Exception('商品未上架');
    		throw new InvalidRequestException('商品未上架');
    		//throw new InternalException('商品未上架');
    	}

    	//是否收藏商品的标示
    	$favored = false;
        
        //账号是否邮箱激活
        $emailActive = 1;
    	// 用户未登录时返回的是 null，已登录时返回的是对应的用户对象
    	if($user = $request->user()){
    		// 从当前用户已收藏的商品中搜索 id 为当前商品 id 的商品
    		$favorProduct = $user->favoriteProducts()->find($product->id);
    		// boolval() 函数用于把值转为布尔值
    		$favored = boolval($favorProduct);

            if(!$user->email_verified_at){ //账号未激活
                $emailActive = 0;
            }
    	}
        //var_dump($favored);exit;

        $reviews = OrderItem::query()
            ->with(['order.user','productSku']) // 预先加载关联关系
            ->where('product_id',$product->id)
            ->whereNotNull('reviewed_at')   // 筛选出已评价的
            ->orderBy('reviewed_at','desc')
            ->limit(10)
            ->get();
        //echo '<pre>';print_r($reviews->toArray());exit;
    	return view('products.show',[
            'product' => $product,
            'favored' => $favored,
            'emailActive' => $emailActive,
            'reviews' => $reviews
        ]);
    }

    //收藏商品
    public function favor(Product $product,Request $request)
    {
    	$user = $request->user();

    	//判断当前用户是否已经收藏了某商品,如果已经收藏则不做任何操作直接返回
    	if($user->favoriteProducts()->find($product->id)){
    		return [];
    	}

    	//否则通过 attach() 方法将当前用户和此商品关联起来。
    	$user->favoriteProducts()->attach($product);

    	return [];
    }

    //取消收藏的商品
    public function disfavor(Product $product,Request $request)
    {
    	$user = $request->user();

    	$user->favoriteProducts()->detach($product);

    	return [];
    }

    //获取当前用户的收藏商品
    public function favorites(Request $request)
    {
    	$products = $request->user()->favoriteProducts()->paginate(16);

    	return view('products.favorites',['products' => $products]);
    }
}
