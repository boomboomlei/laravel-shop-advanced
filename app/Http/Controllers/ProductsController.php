<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\CategoryService;

use App\SearchBuilders\ProductSearchBuilder;
use App\Services\ProductService;


use Illuminate\Pagination\LengthAwarePaginator;
class ProductsController extends Controller
{
    // public function index(Request $request,CategoryService $categoryService)
    // {
        
    //     // 创建一个查询构造器
    //     $builder = Product::query()->where('on_sale', true);
    //     // 判断是否有提交 search 参数，如果有就赋值给 $search 变量
    //     // search 参数用来模糊搜索商品
    //     if ($search = $request->input('search', '')) {
    //         $like = '%'.$search.'%';
    //         // 模糊搜索商品标题、商品详情、SKU 标题、SKU描述
    //         $builder->where(function ($query) use ($like) {
    //             $query->where('title', 'like', $like)
    //                 ->orWhere('description', 'like', $like)
    //                 ->orWhereHas('skus', function ($query) use ($like) {
    //                     $query->where('title', 'like', $like)
    //                         ->orWhere('description', 'like', $like);
    //                 });
    //         });
    //     }

    //     if($request->input('category_id') && $category=Category::find($request->input('category_id'))){
    //         if($category->is_directory){
    //             $builder->whereHas('category',function ($query) use ($category){
    //                 $query->where('path','like',$category->path.$category->id.'-%');
    //             });
    //         }else{
    //             $builder->where('category_id',$category->id);
    //         }
    //     }

    //     // 是否有提交 order 参数，如果有就赋值给 $order 变量
    //     // order 参数用来控制商品的排序规则
    //     if ($order = $request->input('order', '')) {
    //         // 是否是以 _asc 或者 _desc 结尾
    //         if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
    //             // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
    //             if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
    //                 // 根据传入的排序值来构造排序参数
    //                 $builder->orderBy($m[1], $m[2]);
    //             }
    //         }
    //     }

    //     $products = $builder->paginate(16);

    //     return view('products.index', [
    //         'products' => $products,
    //         'filters'  => [
    //             'search' => $search,
    //             'order'  => $order,
    //         ],
    //         'category'=>$category??null,
    //         // 将类目树传递给模板文件
    //         'categoryTree' => $categoryService->getCategoryTree(),
    //     ]);
    // }


    

    public function index_temp(Request $request)
    {
        $page    = $request->input('page', 1);
        $perPage = 16;

        // 构建查询
        $params = [
            'index' => 'products',
            'body'  => [
                'from'  => ($page - 1) * $perPage, // 通过当前页数与每页数量计算偏移值
                'size'  => $perPage,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['on_sale' => true]],
                        ],
                    ],
                ],
            ],
        ];

        // 是否有提交 order 参数，如果有就赋值给 $order 变量
        // order 参数用来控制商品的排序规则
        if ($order = $request->input('order', '')) {
            // 是否是以 _asc 或者 _desc 结尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 根据传入的排序值来构造排序参数
                    $params['body']['sort'] = [[$m[1] => $m[2]]];
                }
            }
        }


        if($request->input('category_id') && $category=Category::find($request->input('category_id'))){
            if($category->is_directory){
                $params['body']['query']['bool']['filter'][]=[
                    'prefix'=>['category_path'=>$category->path.$category->id.'-'],
                ];
            }else{
                $params['body']['query']['bool']['filter'][]=['term'=>['category_id'=>$category->id]];
            }
        }


        if($search=$request->input('search','')){

            $keywords = array_filter(explode(' ', $search));
            $params['body']['query']['bool']['must'] = [];

            foreach ($keywords as $keyword) {
                $params['body']['query']['bool']['must'][]=[
                    [

                        'multi_match'=>[
                            'query'=>$keyword,
                            'fields'=>[
                                'title^3',
                                'long_title^2',
                                'category^2',
                                'description',
                                'skus_title',
                                'skus_description',
                                'properties_value'
                            ],
                        ]

                    ] 
                ];
            }
        }


        if($search || isset($category)){
            $params['body']['aggs']=[
                'properties'=>[
                    'nested'=>[
                        'path'=>'properties',
                    ],
                    'aggs'=>[
                        'properties'=>[
                            'terms'=>[
                                'field'=>'properties.name',
                            ],
                            'aggs'=>[
                                'value'=>[
                                    'terms'=>[
                                        'field'=>'properties.value',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],


            ];
        }

        $properties=[];
       

        $propertyFilters=[];
        if($filterString=$request->input('filters')){
            $filterArray=explode('|',$filterString);
            foreach($filterArray as $filter){
                list($name,$value)=explode(':',$filter);

                $propertyFilters[$name]=$value;
                $params['body']['query']['bool']['filter'][]=[
                    'nested'=>[
                        'path'=>'properties',
                        'query'=>[
                            // ['term'=>['properties.name'=>$name]],
                            // ['term'=>['properties.value'=>$value]],
                            
                            ['term' => ['properties.search_value' => $filter]],
                        ],
                    ],
                ];
            }
        }


        

        $result = app('es')->search($params);
       
        if(isset($result['aggregations'])){
            // dd($result,$result['aggregations']['properties']['properties']['buckets'],collect($result['aggregations']['properties']['properties']['buckets']));
            $properties=collect($result['aggregations']['properties']['properties']['buckets'])->map(function($bucket){
                return [
                    'key'=>$bucket['key'],
                    'values'=>collect($bucket['value']['buckets'])->pluck('key')->all(),
                ];
            })->filter(function($property) use ($propertyFilters){
                return count($property['values'])>1 && !isset($propertyFilters[$property['key']]);
            });
        }

        
        // dd($params,$result['hits']['hits'],collect($result['hits']['hits'])->pluck('_id')->all());

        // 通过 collect 函数将返回结果转为集合，并通过集合的 pluck 方法取到返回的商品 ID 数组
        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();
        // 通过 whereIn 方法从数据库中读取商品数据
        $products = Product::query()
            ->whereIn('id', $productIds)
            // orderByRaw 可以让我们用原生的 SQL 来给查询结果排序
            ->orderByRaw(sprintf("FIND_IN_SET(id,'%s')",join(',',$productIds)))
            ->get();

            // dd($products);
        // 返回一个 LengthAwarePaginator 对象
        $pager = new LengthAwarePaginator($products, $result['hits']['total']['value'], $perPage, $page, [
            'path' => route('products.index', false), // 手动构建分页的 url
        ]);

        return view('products.index', [
            'products' => $pager,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
            'category' => $category??null,
            'properties'=>$properties,
            'propertyFilters' => $propertyFilters,
        ]);
    }



    public function index(Request $request){
        $page=$request->input('page',1);
        $perPage=16;
        $builder=(new ProductSearchBuilder())->onsale()->paginate($perPage,$page);


        if($request->input('category_id')&&$category=Category::find($request->input('category_id'))){
            $builder->category($category);
        }

        if($search=$request->input('search','')){
            $keywords=array_filter(explode(' ',$search));
            $builder->keywords($keywords);
        }

        if($search || isset($category)){
            $builder->aggregateProperties();
        }

        $propertyFilters=[];
        if($filterString=$request->input('filters')){
            $filterArray=explode('|',$filterString);
            foreach($filterArray as $filter){
                list($name,$value)=explode(':',$filter);
                $propertyFilters[$name]=$value;
                $builder->propertyFilter($name,$value);
            }



        }


        if($order=$request->input('order','')){
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 调用查询构造器的排序
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $result=app('es')->search($builder->getParams());

        if(isset($result['aggregations'])){
            // dd($result,$result['aggregations']['properties']['properties']['buckets'],collect($result['aggregations']['properties']['properties']['buckets']));
            $properties=collect($result['aggregations']['properties']['properties']['buckets'])->map(function($bucket){
                return [
                    'key'=>$bucket['key'],
                    'values'=>collect($bucket['value']['buckets'])->pluck('key')->all(),
                ];
            })->filter(function($property) use ($propertyFilters){
                return count($property['values'])>1 && !isset($propertyFilters[$property['key']]);
            });
        }

        
        // dd($params,$result['hits']['hits'],collect($result['hits']['hits'])->pluck('_id')->all());

        // 通过 collect 函数将返回结果转为集合，并通过集合的 pluck 方法取到返回的商品 ID 数组
        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();
        // 通过 whereIn 方法从数据库中读取商品数据
        $products = Product::query()
           // ->whereIn('id',$productIds)
           // ->orderByRaw(sprintf("FIND_IN_SET(id,'%s')",join(',',$productIds)))
            // orderByRaw 可以让我们用原生的 SQL 来给查询结果排序
            ->byIds($productIds)
            ->get();

            // dd($products);
        // 返回一个 LengthAwarePaginator 对象
        $pager = new LengthAwarePaginator($products, $result['hits']['total']['value'], $perPage, $page, [
            'path' => route('products.index', false), // 手动构建分页的 url
        ]);

        return view('products.index', [
            'products' => $pager,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
            'category' => $category??null,
            'properties'=>$properties??[],
            'propertyFilters' => $propertyFilters,
        ]);
    }

    public function show(Product $product, Request $request,ProductService $service)
    {

        // dd($product->toESArray());
        // $builder=(new ProductSearchBuilder())->onSale()->paginate(4,1);
        // foreach($product->properties as $property){
        //     $builder->propertyFilter($property->name,$property->value,'should');
        // }

        // $builder->minShouldMatch(ceil(count($product->properties)/2));
        // $params=$builder->getParams();

        // $params['body']['query']['bool']['must_not']=[['term'=>['id'=>$product->id]]];

        // $result=app('es')->search($params);

        // $similarProductIds=collect($result['hits']['hits'])->pluck('_id')->all();

        $similarProductIds=$service->getSimilarProductIds($product,4);

        $similarProducts=Product::query()
                // ->whereIn('id',$similarProductIds)
                // ->orderByRaw(sprintf("FIND_IN_SET(id,'%s')",join(',',$similarProductIds)))
                ->byIds($similarProductIds)
                ->get();
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;
        // 用户未登录时返回的是 null，已登录时返回的是对应的用户对象
        if($user = $request->user()) {
            // 从当前用户已收藏的商品中搜索 id 为当前商品 id 的商品
            // boolval() 函数用于把值转为布尔值
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        $reviews = OrderItem::query()
            ->with(['order.user', 'productSku']) // 预先加载关联关系
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at') // 筛选出已评价的
            ->orderBy('reviewed_at', 'desc') // 按评价时间倒序
            ->limit(10) // 取出 10 条
            ->get();

        // 最后别忘了注入到模板中
        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews,
            'similar' => $similarProducts,
        ]);
    }

    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
    }

    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return [];
    }
}
