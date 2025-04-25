<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categories;

use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    // get all categories
    public function index()
    {
        $categories = Categories::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 200, // success
            'message' => 'All Categories',
            'data' => $categories // return all categories
        ]);
    }
    // create category
    public function store(Request $request)
    {
        // validate request
        try {
            $data = $request->validate([
                'name' => 'required|string',

            ]);

            // create category
            $category = Categories::create([
                'name' => $data['name'],
                'status' => 1
            ]);
            return response()->json([
                'status' => 201, // created success
                'message' => 'Category Created',
                'data' => $category // return created category
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 400, // bad request
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500, // internal server error
                'message' => 'Server Error',
                'errors' => $e->getMessage()
            ]);
        }
    }

    // get single category
    public function show($id)
    {
        $category = Categories::find($id);
        if (!$category) {
            return response()->json([
                'status' => 404, // not found
                'message' => 'Category Not Found',
            ]);
        }

        return response()->json([
            'status' => 200, // success
            'message' => 'Category',
            'data' => $category // return single category
        ]);
    }
    // update category method
    public function update($id, Request $request)
    {
        try {
            // Get category
            $category = Categories::find($id);

            if ($category === null) {
                return response()->json([
                    'status' => 404, // Not Found
                    'message' => 'Category Not Found',
                    'data' => [],

                ], 404);
            }

            // Validate request
            $validate = Validator::make($request->all(), [
                'name' => 'string', // Corrected validation rule
                'status' => 'boolean' // Corrected validation rule
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 400, // Bad Request
                    'message' => 'Validation Error',
                    'errors' => $validate->errors()
                ]);
            }
            // Update category
            $category->name = $request->name;
            $category->status = $request->status;
            $category->save();


            return response()->json([
                'status' => 200, // Success
                'message' => 'Category Updated',
                'data' => $category // Return updated category
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 400, // Bad Request
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500, // Internal Server Error
                'message' => 'Server Error',
                'errors' => $e->getMessage()
            ]);
        }
    }

    // delete category
    public function destroy($id)
    {
        $category = Categories::find($id);
        if (!$category) {
            return response()->json([
                'status' => 404, // not found
                'message' => 'Category Not Found',
            ]);
        }
        $category->delete();
        return response()->json([
            'status' => 200, // success
            'message' => 'Category Deleted💔💔',
        ]);
    }
}
