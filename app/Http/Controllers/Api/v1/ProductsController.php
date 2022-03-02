<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\CategoryRequest;
use App\Http\Requests\v1\ProductRequest;
use App\Http\Resources\v1\CategoryResource;
use App\Http\Resources\v1\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ProductsController extends Controller{

    public function index(Request $request){
        $productBuilder = Product::query();

        # полное или частичное совпадение по названию товара
        if ($request->has('search.product_title')){
            $productBuilder->where('title', 'LIKE', '%'. $request->get('search')['product_title'] .'%');
        }

        # минимальная цена
        if ($request->has('search.price_from')){
            $productBuilder->where('price', '>=', $request->get('search')['price_from']);
        }

        # максимальная цена
        if ($request->has('search.price_to')){
            $productBuilder->where('price', '<=', $request->get('search')['price_to']);
        }

        # опубликованные / неопубликованные
        if ($request->has('search.is_published')){
            $productBuilder->where([
                'is_published' => $request->get('search')['is_published']
            ]);
        }

        # не удаленные (предполагается что изначально показываются все)
        if (!$request->has('search.not_trashed')){
            $productBuilder->withTrashed();
        }

        # по названию категории
        if ($request->has('search.category_title')){
            $productBuilder->whereHas('categories', function($query) use ($request){
                $query->where('title', 'LIKE', '%'. $request->get('search')['category_title'] .'%');
            });
        }

        # по ID категории
        if ($request->has('search.category_id')){
            $productBuilder->whereHas('categories', function($query) use ($request){
                $query->where([
                    'id' => $request->get('search')['category_id']
                ]);
            });
        }

        return ProductResource::collection($productBuilder->with('categories')->get());

    }

    public function store(ProductRequest $request){
        $product = new Product;
        $product->fill($request->all());

        if ($product->save()){
            $product->categories()->attach($request->get('categories'));

            return ProductResource::make($product->load('categories'));
        }

        return JsonResource::make([
            'status' => 'error',
            'message' => 'Ошибка добавления товара'
        ]);
    }

    public function show($id){
        try {
            $product = Product::query()
                ->with('categories')
                ->where(['id' => $id])
                ->firstOrFail();

        } catch (ModelNotFoundException $e){
            return JsonResource::make([
                'status' => 'error',
                'message' => 'Товар не найден'
            ]);
        }

        return ProductResource::make($product->load('categories'));
    }

    public function update(Product $product, ProductRequest $request){
        $product->fill($request->all());

        if ($product->save()){
            $product->categories()->sync($request->get('categories'));

            return ProductResource::make($product->load('categories'));
        }

        return JsonResource::make([
            'status' => 'error',
            'message' => 'Ошибка обновления товара'
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return JsonResource::make([
            'status' => 'success',
        ]);
    }
}
