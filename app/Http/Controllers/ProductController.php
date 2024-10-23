<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    // List all products
    public function index(Request $request)
    {
        $products = Product::paginate(10); // Example with pagination
        return response()->json($products);
    }

    // Show a single product
    public function products()
    {
        $products = Product::all();
        if(!$products){
            return response()->json(['errors' => "Product creation failed"]);
        }
        return response()->json(['message' => "Product fetched successfully", "data"=>$products], 200);
    }

    // Show a single product by ID
        public function show($id)
        {
            // Find the product by ID or return 404 if not found
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            // Return the product as a JSON response
            return response()->json(['product' => $product], 200);
        }


    // Create a new product
    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request ->all(),[
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'category' => 'required|string',
            'image' => 'required|mimes:jpeg,jpg,png,image,max:1024'
        ]);

        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

         // Create the new product using the validated data
         $formfields = [
            'product_name' => $request->product_name,
            'price' => $request->price,
            'description' => $request->description,
            'category' => $request->category,
        ];

        $image = uniqid() . '-' . 'product-image' . '.' . $request->image->extension();
        $request->image->move(public_path('products'), $image);

        $formfields['image'] = $image;

        $product = Product::create($formfields);  // Create and save the product to the database


        if (!$product){
            return response()->json(['errors' => "Product creation failed"], 403);
        }else {
            return response()->json(['message' => "Product created successfully", "data"=>$product], 201);
        }
    }

    // Update an existing product
    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make($request ->all(),[
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'category' => 'required|string',
            'image' => 'required|mimes:jpeg,jpg,png,image,max:1024'
            // Add more validation rules as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $formfields = [
            'product_name' => $request->product_name,
            'price' => $request->price,
            'description' => $request->description,
            'category' => $request->category,
        ];

        $image = uniqid() . '-' . 'product-image' . '.' . $request->image->extension();
        $request->image->move(public_path('products'), $image);

        $formfields['image'] = $image;

        $product = Product::where('id', $id)->update([
            'product_name' => $request->product_name,
            'price' => $request->price,
            'description' => $request->description,
            'category' => $request->category,
            'image' => $request->image
        ]);

        if (!$product){
            return response()->json(['errors' => "Product update failed"], 400);
        }else {
            return response()->json(['message' => "Product update successfully", "data"=>$product], 201);
        }

        // $product = Product::findOrFail($id);
        // $product->update($request->all());
        // return response()->json($product);
    }

    // Delete a product
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
