<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'full_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'termin' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:0',
            'code' => 'required|string|unique:products,code',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'composition' => 'nullable|array',
            'composition.*' => 'numeric|min:0|max:100'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $data['image'] = $imageName;
        }

        // Process composition
        if ($request->composition) {
            $composition = [];
            foreach ($request->composition as $ingredient => $percentage) {
                if ($percentage > 0) {
                    $composition[$ingredient] = $percentage;
                }
            }
            $data['composition'] = $composition;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Продукт успішно створено!');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'full_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'termin' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:0',
            'code' => 'required|string|unique:products,code,' . $product->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'composition' => 'nullable|array',
            'composition.*' => 'numeric|min:0|max:100'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
                unlink(public_path('images/products/' . $product->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $data['image'] = $imageName;
        }

        // Process composition
        if ($request->composition) {
            $composition = [];
            foreach ($request->composition as $ingredient => $percentage) {
                if ($percentage > 0) {
                    $composition[$ingredient] = $percentage;
                }
            }
            $data['composition'] = $composition;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Продукт успішно оновлено!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image
        if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
            unlink(public_path('images/products/' . $product->image));
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Продукт успішно видалено!');
    }

    /**
     * Toggle product active status
     */
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'активовано' : 'деактивовано';
        return redirect()->back()->with('success', "Продукт успішно {$status}!");
    }
}