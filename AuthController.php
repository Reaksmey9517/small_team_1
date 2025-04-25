<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {

try {
            // Validate the request
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6'
            ]);

            // Create a new user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return response
            return response()->json([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 201);

        } catch (ValidationException $e) {
            // FIXED: Corrected the syntax for returning validation errors
            return response()->json(['error' => $e->errors()], 422);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong! 💔'], 500);
        }
    }

    // Login a user
    public function login(Request $request)
    {
        try {
            // Validate the request
            $data = Validator::make($request->all(),[
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if($data->fails()){
                return response()->json([
                    'status' => 400,
                    'error' => $data->errors()
                ], 400);
            }
            // Find the user by email
            if(Auth::attempt(['email' =>$request->email, 'password'=>$request->password])){
                $user = User::find(Auth::user()->id);
                // check if admin
                if($user->role == 'admin'){
                    $token = $user->createToken('token')->plainTextToken;

                    return response()->json([
                        'stauts' => '200',   
                        'token' => $token,
                        'id' => $user->id,
                        'name' => $user->name                
                    ], 200);
                }
            }else{
                return response()->json([
                    'status' => 401,
                    'message' => "You are not authorized to access admin panel"
                ], 401);
            }
            
            

        } catch (ValidationException $e) {
            // ✅ Fixed syntax error in status code
            return response()->json(['error' => $e->errors()], 422);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong! 💔'], 500);
        }
    }

}
