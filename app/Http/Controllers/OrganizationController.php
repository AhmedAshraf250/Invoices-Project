<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        $organizations = Organization::query()
            ->latest('id')
            ->get();

        return view('organizations.index', compact('organizations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:organizations,name'],
            'description' => ['nullable', 'string', 'max:2000'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        Organization::query()->create([
            ...$validated,
            'commission_rate' => $request->input('commission_rate', 0),
            'created_by' => $request->user()?->name,
        ]);

        return to_route('organizations.index')
            ->with('success', __('organizations.messages.created'));
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:organizations,name,'.$organization->id],
            'description' => ['nullable', 'string', 'max:2000'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $organization->update([
            ...$validated,
            'commission_rate' => $request->input('commission_rate', 0),
        ]);

        return to_route('organizations.index')
            ->with('success', __('organizations.messages.updated'));
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        if ($organization->invoices()->exists() || $organization->products()->exists()) {
            return to_route('organizations.index')
                ->with('error', __('organizations.messages.cannot_delete_with_dependencies'));
        }

        $organization->delete();

        return to_route('organizations.index')
            ->with('success', __('organizations.messages.deleted'));
    }

    public function getProducts(Organization $organization): JsonResponse
    {
        $products = $organization->products()
            ->select('id', 'name', 'commission_rate')
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }
}
