<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\order_items;
use App\Models\orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    //
    public function SaveOrder(Request $request)
    {

        if (!empty($request->cart)) {
            // save order in db
            $order = new orders();
            $order->name = $request->name;
            $order->email = $request->email;
            $order->address = $request->address;
            $order->mobile = $request->mobile;
            $order->city = $request->city;
            $order->zip = $request->zip;
            $order->subTotal = $request->subTotal;
            $order->grand_total = $request->grand_total;
            $order->shipping = $request->shipping;
            $order->discount = $request->discount;
            $order->payment_status = $request->payment_status;
            $order->user_id = $request->user()->id;
            $order->status = $request->status;

            // save it to database
            $order->save();
            // save order items
            foreach ($request->cart as $item) {
                if (!isset($item['product_id'])) {
                    continue; // or return an error response
                }
                $orderItem = new order_items();
                $orderItem->order_id = $order->id;
                $orderItem->price = $item['quantity'] * $item['price'];
                $orderItem->unit_price = $item['price'];
                $orderItem->qty = $item['quantity'];
                $orderItem->product_id = $item['product_id'];
                $orderItem->name = $item['title'];
                // $orderItem->name = $item['name'];
                $orderItem->save();
            }

            return response()->json([
                "status" => 200,
                "message" => "You have succefully place order ",
                "data" => $order,
                "order_items" => $order->item,
            ], 200);
        } else {
            return response()->json([
                "status" => 400,
                "message" => "Your cart is empty",
                "data" => null,
                "order_items" => null,
            ], 400);
        }

    }
}


