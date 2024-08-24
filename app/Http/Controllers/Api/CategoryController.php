<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    // ini untuk munculin di halaman depan
    public function index()
    {
        $categories = Category::withCount('recipes')->get(); // ambil category bersama dengan jumlah recipes yg ada di category tsb
        return CategoryResource::collection($categories); // lempar $categories ke CategoryResource | bentuknya collection karna akan ada banyak data di dalam category (Categories hasMany Recipes)
    }

    // untuk show details di halaman detail categpry
    public function show (Category $category)
    {
        $category->load(['recipes.category', 'recipes.author']); // ambil seluruh data di recipes bersama dgn category dan recipes bersama dengan author yg berada di category bersangkutan
        $category->loadCount('recipes')->get(); // ambil jumlah recipes yg berada di category tsb
        return new CategoryResource($category); // gunakan new bukan collection, karna di function show ini bertujuan untuk menunjukan halaman detail sehingga hanya satu category yg di tampilkan
    }
}
