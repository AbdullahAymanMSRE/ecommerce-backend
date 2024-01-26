<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductTagRequest;
use App\Http\Requests\UpdateProductTagRequest;
use App\Http\Resources\ProductTagResource;
use App\Models\ProductTag;
use Illuminate\Support\Facades\File;

class ProductTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductTagResource::collection(ProductTag::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductTagRequest $request)
    {
        $newTag = new ProductTag();
        $newTag->tag_name = $request->tagName;
        $newTag->product_id = $request->productId;

        $image = $request->image;
        $imageName =  time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('tags'), $imageName);

        $newTag->image = $imageName;

        $newTag->save();

        return new ProductTagResource($newTag);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductTag $productTag)
    {
        return new ProductTagResource($productTag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductTagRequest $request, ProductTag $productTag)
    {
        if (isset($request->tagName)) $productTag->tag_name = $request->tagName;
        if (isset($request->productId)) $productTag->product_id = $request->productId;

        if (isset($request->image)) {

            File::delete(public_path('tags/') . $productTag->image);
            $image = $request->image;
            $imageName =  time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('tags'), $imageName);

            $productTag->image = $imageName;
        }

        $productTag->save();

        return new ProductTagResource($productTag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductTag $productTag)
    {
        File::delete(public_path('tags/') . $productTag->image);

        return $productTag->delete();
    }
}
