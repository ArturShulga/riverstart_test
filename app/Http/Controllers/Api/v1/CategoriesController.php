<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\CategoryRequest;
use App\Http\Resources\v1\CategoryResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CategoriesController extends Controller{

    public function index(Request $request){
        $categoriesBuilder = Category::query();

        if ($request->has('with_trashed')){
            $categoriesBuilder->withTrashed();
        }

        if ($request->has('only_trashed')){
            $categoriesBuilder->onlyTrashed();
        }

        return CategoryResource::collection($categoriesBuilder->withCount('products')->get());
    }

    public function store(CategoryRequest $request){
        $category = new Category;
        $category->fill($request->all());

        if ($category->save()){
            return CategoryResource::make($category->load('products'));
        }

        return JsonResource::make([
            'status' => 'error',
            'message' => 'Ошибка добавления категории'
        ]);
    }

    public function show(Category $category){
        return CategoryResource::make($category->load('products'));
    }

    public function update(Category $category, CategoryRequest $request){
        $category->fill($request->all());

        if ($category->save()){
            return CategoryResource::make($category->load('products'));
        }

        return JsonResource::make([
            'status' => 'error',
            'message' => 'Ошибка обновления категории'
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count()) {
            return JsonResource::make([
                'status' => 'error',
                'message' => 'Категория имеет товары и не может быть удалена',
            ]);
        }

        $category->delete();

        return JsonResource::make([
            'status' => 'success',
        ]);
    }
}
