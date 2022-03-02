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
