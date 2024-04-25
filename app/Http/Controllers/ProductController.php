<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // echo(Product::all());
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name'=>'required',
            'product_price'=>'required',
            'product_slug'=>'required'
        ]);
        return Product::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Product::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        $product->update($request->all());
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    
        return Product::destroy($id);
    }

     /**
     * Search for  resource from storage.
     * @param str $name
     * @return \Illuminate\Http\Response
     */
    public function search(string $product_name)
    {
        return Product::where('product_name', 'like', '%'.$product_name.'%')->get();
    }
}
