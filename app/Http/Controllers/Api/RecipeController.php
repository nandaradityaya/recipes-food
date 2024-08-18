<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    //
    public function index()
    {
        $recipes = Recipe::with(['photos', 'category'])->get(); // ambil semua yg berelasi
        return RecipeResource::collection($recipes); // kirim $recipes ke RecipeResource
    }

    // model binding
    public function show(Recipe $recipe)
    {
        $recipe->load(['category', 'photos', 'author', 'tutorials', 'recipeIngredients.ingredient']); // panggil model Recipe dan load semua yg berelasi dengan recipe | recipeIngredients.ingredient karena dia many to many, jadi masuk ke pivot table dulu yaitu recipeIngredients lalu masuk ke ingredients untuk ambil datanya
        return new RecipeResource($recipe); // kirim detailsnya ke RecipeResource
    }
}
