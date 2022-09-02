<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use App\services\ProductService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'message' => 'Products retrieved successfully',
            'products' => $this->productService->getAllProducts()
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
        $imagePath = uploadFile($request);

        // complete store payload
        $payload = $request->validated();
        $payload['image'] = $imagePath;

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $this->productService->create($this->productDataToStore($payload))
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
        // unlink old imageDir
        removeFile($product->image);
        // store image
        $imagePath = uploadFile($product);

        $data = $request->validated();
        $data['image'] = $imagePath;

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $this->productService->update($product)
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
        removeFile($product->image) ? $product->delete() : null;

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
        return response()->json([
            'message' => 'Product found successfully',
            'product' => $this->productService->search($slug)
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
            'slug' => Str::slug($data['name'] . '-' . time())
        ];
    }
}
