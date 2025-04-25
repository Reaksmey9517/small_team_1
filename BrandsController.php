<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PharIo\Manifest\Author;

class BrandsController extends Controller
{
    // get all brands
    public function index()
    {
        $brands = Brands::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 200, // success
            'message' => 'All Brands',
            'data' => $brands // return all brands
        ]);
    }

    // create brand or store
    public function store(Request $request)
    {
       try{
            // validate request
            $data = $request->validate([
                'name' => 'required|string',
                'status' => 'required'
            ]);

            // create brand to database
            $brand = Brands::create([
                'name' => $data['name'],
                'status' => 1
            ]);
            return response()->json([
                'status' => 201, // created success
                'message' => 'Brand Created',
                'data' => $brand // return created brand
            ]);
       }catch(ValidationException $e){
            return response()->json([
                'status' => 400, // bad request
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ]);
       }catch(Exception $e){
            return response()->json([
                'status' => 500, // internal server error
                'message' => 'Server Error',
                'errors' => $e->getMessage()
            ]);
       }
    }

    // show brand by id or single brand❤️
    public function show($id)
    {
        $brand = Brands::find($id);
        // check if brand exists
        if ($brand == null) {
            return response()->json([
                'status' => 404, // not found
                'message' => 'Brand Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 200, // success
            'message' => 'Brand Found',
            'data' => $brand // return brand
        ], 200);
    }

    // update brand by id ❤️❤️🌍 0_0 ----> Yeng
    public function update(Request $request, $id)
    {
        try{

            // validate request
        $validate = Validator::make($request->all(),[
            'name' => 'required|string', 

        ]);

        // find brand by id
        $brand = Brands::find($id);
        // check if brand exists
        if ($brand == null) {
            return response()->json([
                'status' => 404, //  not found 💔💔
                'message' => 'Brand Not Found',
            ], 404);
        }

        // check if validation fails
        if($validate->fails()){
            return response()->json([
                'status' => 400, // bad request
                'message' => 'Validation Error',
                'errors' => $validate->errors()
            ], 400);
        }
        // update brand
        $brand->name = $request->name;
        $brand->status = $request->status;
        $brand->save();
        return response()->json([
            'status' => 200, // success
            'message' => 'Brand Updated',
            'data' => $brand // return updated brand
        ], 200);
        }catch(ValidationException $e){
            return response()->json([
                'status' => 400, // bad request
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => 500, // internal server error
                'message' => 'Server Error',
                'errors' => $e->getMessage()
            ]);
        }
    }

    // delete brand by id
    public function destroy($id)
    {
        // find brand by id
        $brand = Brands::find($id);
        // check if brand exists
        if ($brand == null) {
            return response()->json([
                'status' => 404, // not found
                'message' => 'Brand Not Found',
            ], 404);
        }
        // delete brand
        $brand->delete();
        return response()->json([
            'status' => 200, // success
            'message' => 'Brand Deleted',
        ], 200);
    }

}
