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
        $products = Product::all();
        return view('pages.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = time() . '.' . $request->image->extension();

        $request->image->move(public_path('images'), $imageName);

        $product = Product::create(
            [
                'name' => $request->name,
                'price' => $request->price,
                'stock' => $request->stock,
                'image' => $imageName,
            ]
        );

        return redirect('/dashboard/product')->with('success', 'Product created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::find($id);
        return view('pages.product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return redirect('/dashboard/product')->with('success', 'Product updated successfully');
    }

    public function updateStock(Request $request, $id)
    {
        $product = Product::find($id);

        $product->update([
            'stock' => $request->stock + $product->stock,
        ]);

        return redirect('/dashboard/product')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return redirect('/dashboard/product')->with('success', 'Product deleted successfully');
    }
}
