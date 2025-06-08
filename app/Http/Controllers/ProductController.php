<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'interest_rate' => 'required|numeric|min:0',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0',
            'term' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive'
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'interest_rate' => 'required|numeric|min:0',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0',
            'term' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive'
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
} 