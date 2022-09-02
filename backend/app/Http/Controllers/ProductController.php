<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::paginate(config('app.pagination.per_page'));

        return response()->json([
            'message' => 'Products retrieved successfully',
            'products' => $products
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        // store image
        $request->file('image')
        ->storePubliclyAs('image/products', $request->file('avatar')
        ->getClientOriginalName(), 'public');

        $data = $request->validated();
        $data['image'] = $request->file('avatar')->getClientOriginalName();

        $product = Product::create($this->productDataToStore($data));

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'message' => 'Product found successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        chmod(public_path($product->image), 0777);
        // unlink old imageDir
        unlink(public_path('images/' . $product->image));

        // store image
        $image = $request->file('image');
        $image_name = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $image_name);
        $data = $request->validated();
        $data['image'] = 'images/' . $image_name;

        $product->update($this->productDataToStore($data));

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        chmod(public_path('images/' . $product->image), 0777);

        // unlink old imageDir
        unlink(public_path($product->image));
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Search for a product by its slug
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($slug)
    {
        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Product found successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Get product data to store
     *
     * @param  array  $data
     * @return array
     */
    private function productDataToStore(array $data)
    {
        return [
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'user_id' => $data['user_id'],
            'image' => $data['image'],
            'quantity' => $data['quantity'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'slug' => Str::slug($data['name']),
        ];
    }
}
