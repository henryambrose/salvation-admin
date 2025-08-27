<?php

namespace Modules\Fund\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Fund\Models\PaymentMethod;
use Modules\Members\Models\User;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of payment methods.
     */
    public function index(Request $request): Response
    {
        $query = PaymentMethod::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Handle archived filter
        if ($request->get('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Apply pagination
        $perPage = $request->get('perPage', 15);
        $paymentMethods = $query->paginate($perPage);

        return Inertia::render('PaymentMethods/Index', [
            'paymentMethods' => $paymentMethods,
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'fetchUrl' => route('fund.payment-methods.index'),
        ]);
    }

    /**
     * Show the form for creating a new payment method.
     */
    public function create(): Response
    {
        return Inertia::render('Fund/PaymentMethods/Create');
    }

    /**
     * Store a newly created payment method.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        PaymentMethod::create($validated);

        return redirect()->route('fund.payment-methods.index')
            ->with('success', 'Payment method created successfully.');
    }

    /**
     * Display the specified payment method.
     */
    public function show(PaymentMethod $paymentMethod): Response
    {
        $paymentMethod->load(['createdBy', 'updatedBy']);

        return Inertia::render('Fund/PaymentMethods/Show', [
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Show the form for editing the specified payment method.
     */
    public function edit(PaymentMethod $paymentMethod): Response
    {
        return Inertia::render('Fund/PaymentMethods/Edit', [
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Update the specified payment method.
     */
    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name,' . $paymentMethod->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['updated_by'] = Auth::id();

        $paymentMethod->update($validated);

        return redirect()->route('fund.payment-methods.index')
            ->with('success', 'Payment method updated successfully.');
    }

    /**
     * Remove the specified payment method.
     */
    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        \Log::info('Attempting to delete payment method', [
            'id' => $paymentMethod->id,
            'name' => $paymentMethod->name,
            'related_contributions_count' => $paymentMethod->familyContributions()->count()
        ]);

        try {
            $paymentMethod->delete();
            
            \Log::info('Payment method deleted successfully', [
                'id' => $paymentMethod->id,
                'deleted_at' => $paymentMethod->deleted_at
            ]);

            return back()->with('success', 'Payment method deleted successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete payment method', [
                'id' => $paymentMethod->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Failed to delete payment method: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore the specified payment method.
     */
    public function restore(int $id): RedirectResponse
    {
        $paymentMethod = PaymentMethod::onlyTrashed()->findOrFail($id);
        $paymentMethod->restore();

        return back()->with('success', 'Payment method restored successfully.');
    }

    /**
     * Permanently delete the specified payment method.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $paymentMethod = PaymentMethod::onlyTrashed()->findOrFail($id);
        
        // Check if payment method is being used
        if ($paymentMethod->familyContributions()->count() > 0) {
            return redirect()->route('fund.payment-methods.index')
                ->with('error', 'Cannot permanently delete payment method. It is being used by contributions.');
        }

        $paymentMethod->forceDelete();

        return redirect()->route('fund.payment-methods.index')
            ->with('success', 'Payment method permanently deleted.');
    }

    /**
     * Toggle the active status of a payment method.
     */
    public function toggleStatus(PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->update([
            'is_active' => !$paymentMethod->is_active,
            'updated_by' => Auth::id(),
        ]);

        $status = $paymentMethod->is_active ? 'activated' : 'deactivated';

        return redirect()->route('fund.payment-methods.index')
            ->with('success', "Payment method {$status} successfully.");
    }
}
