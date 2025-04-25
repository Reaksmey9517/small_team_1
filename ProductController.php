<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Categories;
use App\Models\products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getAllProduct(Request $request)
    {
        $product = products::orderBy('created_at', 'desc')
        ->where('status', 1);

        // filter by category
        if(!empty($request->category)){
            $cateArr = explode(',', $request->category);
            $product = $product->whereIn('category_id', $cateArr);

        }
        
        // filter by brand
        if(!empty($request->brands)){
            $brandArr = explode(',', $request->brands);
            $product = $product->whereIn('brand_id', $brandArr);
        }
        
        $product = $product->get();
        return response()->json([
            'status' => 200,
            'message' => 'All Products',
            'data' => $product
        ], 200);
    }
    
    // last product
    public function lastProducts()
    {
        $product = products::orderBy('created_at', 'desc')
        ->where('status', 1)->limit(8)
        ->get();
        // dd($product);
       //
        return response()->json([
            'status' => true,
            'message' => 'Last Products',
            'data' => $product
        ], 200);
    }
    // feature products
    public function featureProducts()
    {
        $product = products::orderBy('created_at', 'desc')
        ->where('is_feature', 'yes')->limit(8)
        ->get();
        // dd($product);
        //
        return response()->json([
            'status' => true,
            'message' => 'Feature Products',
            'data' => $product
        ], 200);
    }
    // get category 
    public function getCategory()
    {
        $category = Categories::orderBy('name', 'asc')
        ->where('status', 1)
        ->get();
        // dd($product);
        //
        return response()->json([
            'status' => true,
            'message' => 'all categories',
            'data' => $category
        ], 200);
    }
    // get brands
    public function getBrands()
    {
        $brands = Brands::orderBy('name', 'asc')
        ->where('status', 1)
        ->get();
        // dd($product);
        //
        return response()->json([
            'status' => true,
            'message' => 'all brands',
            'data' => $brands
        ], 200);
    }
    // get product detail
    public function getProductDetail($id)
    {
        $product = products::find($id);
        // check if product dont exist
        if($product == null){
            return response()->json([
                'status' => 404,
                'message' => 'Product Not Found',
            ], 404);
        }
       
        return response()->json([
            'status' => true,
            'message' => 'Product Detail',
            'data' => $product
        ], 200);
    }
}
