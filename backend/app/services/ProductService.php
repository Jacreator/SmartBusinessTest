<?php

namespace App\services;

use App\Models\Product;

class ProductService
{
    private $productModel;
    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function create($productPayloay)
    {
        return $this->productModel->create($productPayloay);
    }

    public function update($productPayloay)
    {
        return $this->productModel->update($productPayloay);
    }

    public function delete($productPayloay)
    {
        return $this->productModel->delete($productPayloay);
    }

    public function getAllProducts()
    {
        return $this->productModel->paginate(config('app.pagination.per_page'));
    }

    public function search($slug)
    {
        return $this->productModel->where('slug', 'like', '%' . $slug . '%')->first() ?? "not found";
    }
}
