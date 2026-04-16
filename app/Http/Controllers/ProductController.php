<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $organizations = Organization::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with('organization:id,name')
            ->latest('id')
            ->get();

        return view('products.index', compact('products', 'organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('products', 'name')->where(
                    fn ($query) => $query->where('organization_id', $request->integer('organization_id'))
                ),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
        ]);

        Product::query()->create($validated);

        return to_route('products.index')
            ->with('success', __('products.messages.created'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('products', 'name')
                    ->where(fn ($query) => $query->where('organization_id', $request->integer('organization_id')))
                    ->ignore($product->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
        ]);

        $product->update($validated);

        return to_route('products.index')
            ->with('success', __('products.messages.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return to_route('products.index')
            ->with('success', __('products.messages.deleted'));
    }
}
