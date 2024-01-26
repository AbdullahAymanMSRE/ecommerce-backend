<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Support\Facades\File;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductResource::collection(Product::with('images')->get());
    }

    /**
     * Store a newly created Product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $newProduct = new Product();
        $newProduct->rating = 0;
        $newProduct->name = $request->name;
        $newProduct->price = $request->price;
        $newProduct->quantity = $request->quantity;
        $newProduct->color_id = $request->colorId;
        $newProduct->category_id = $request->categoryId;
        $newProduct->save();

        $imagesList = [];
        $i = 0;
        if (isset($request->images)) {

            foreach ($request->images as $image) {
                $newImg = new Image();
                $imageName = "$i" . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('products'), $imageName);
                $newImg['image_url'] = $imageName;
                $imagesList[] = $newImg;
                $i++;
            }
        }

        $newProduct->images()->saveMany($imagesList);
        return new ProductResource(Product::with(['images', 'category', 'color'])->find($newProduct->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource(Product::with(['images', 'category', 'color'])->find($product->id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $productObj = Product::with('images')->find($product->id);
        foreach ($productObj->images as $image) {
            File::delete(public_path('products/') . $image->image_url);
            $image->delete();
        }

        return $productObj->delete();
    }
}
