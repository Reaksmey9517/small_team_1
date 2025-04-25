<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index(){
        $user = User::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 200, // success
            'message' => 'All Users',
            'data' => $user // return all brands
        ]);
    }
}
