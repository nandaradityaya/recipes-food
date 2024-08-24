<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //
    public function index (Request $request) {
        $query = $request->input('query'); // ambil yg di inputkan oleh user | paramsnya masukin query, karna harus sesuai. contoh: localhost:8000/api/recipes/search?query=burger . search?QUERY jangan sampe beda
        $recipes = Recipe::with('author')->where('name', 'LIKE', "%{$query}%")->get(); // cari value yg 'name' nya mirip berdasarkan apa yg di inputkan oleh user dan ambil juga relasi dari table author
        return RecipeResource::collection($recipes); // kirim datanya searchnya ke RecipeResource dan jadikan sbg collection
    }
}
