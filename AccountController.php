<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\orders;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    // register new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);
        // check validation
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()
            ], 400);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'customer';
        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'User registered successfully',
            'data' => $user
        ], 400);
    }

    // Login a user
    public function login(Request $request)
    {
        try {
            // Validate the request
            $data = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if ($data->fails()) {
                return response()->json([
                    'status' => 400,
                    'error' => $data->errors()
                ], 400);
            }
            // Find the user by email
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = User::find(Auth::user()->id);
                // check if admin
                if ($user->role == 'customer') {
                    $token = $user->createToken('token')->plainTextToken;

                    return response()->json([
                        'stauts' => '200',
                        'token' => $token,
                        'id' => $user->id,
                        'name' => $user->name
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 401,
                        'message' => "You are not authorized to access admin panel"
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => "Either email/password in incorrenct"
                ], 401);
            }
        } catch (ValidationException $e) {
            // ✅ Fixed syntax error in status code
            return response()->json(['error' => $e->getMessage()], 422);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong! 💔'], 500);
        }
    }

    // get order details
    public function getOrderDetails($id, Request $request)
    {
        $order = orders::where([
            'user_id' => $request->user()->id,
            'id' => $id
        ])->with('item')->first();

        // check if order don't exist
        if ($order == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found',
                'data' => []
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Order found',
                'data' => $order
            ], 200);
        }

    }
}
