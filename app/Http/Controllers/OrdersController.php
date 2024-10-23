<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;


class OrdersController extends Controller
{
    // Place a new order
    public function placeOrder(Request $request)
    {
        dd($request->all());

        // Validate the request data
        $request->validate([
            'products' => 'required|array', // An array of product IDs and quantities
            'total_price' => 'required|numeric',
        ]);
        // dd($request->all());
        // Create the order
        $order = Order::create([
            'user_id' => Auth::id(), // Assuming you are using authentication
            'products' => json_encode($request->products), // Store products as JSON
            'total_price' => $request->total_price,
        ]);

          // Check if the order was created successfully
            if (!$order) {
                return response()->json(['error' => 'Order creation failed'], 400);
            }
            return response()->json(['message' => 'Order placed successfully', 'order' => $order], 201);
            }

            // List all orders (for an authenticated user)
        public function orders()
        {
            $orders = Order::where('user_id', Auth::id())->get();
            // $orders = Order::where('user_id', id())->get();
            return response()->json($orders);
        }

        public function deleteOrder($id)
    {
        // Find the order by its ID
        $order = Order::find($id);

        // Check if the order exists
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Delete the order and its associated items if necessary
        $order->orderItems()->delete(); // Assuming there's a relationship to delete the items
        $order->delete(); // Delete the order itself

        // Return a response indicating success
        return response()->json(['message' => 'Order deleted successfully'], 200);
    }

}

