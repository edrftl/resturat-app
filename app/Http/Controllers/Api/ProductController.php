<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->query('categoryId');
        $items = Product::with(["category","product_images"])
            ->where("category_id","=",$id)->get();
        return response()->json($items) ->header('Content-Type', 'application/json; charset=utf-8');
    }

    public function store(Request $request)
    {
        if ($request->input('name')
            && $request->input('description')
            && $request->input('price')
            && $request->input('quantity')
            && $request->input('category_id')!= "") {

            $item = Product::create(['name' => $request->input('name')
                ,'description' => $request->input('description')
                ,'price' => $request->input('price')
                ,'quantity' => $request->input('quantity')
                ,'category_id' => $request->input('category_id')]);
            return response()->json($item, 201);
        }
        return response()->json("Bad request", 400);
    }


    public function destroy(int $id)
    {
        $product = Product::find($id);
        $product->delete();
        return response()->json(['message' => 'Category and associated images deleted successfully']);
    }
}
