<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class ResellerPortalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('reseller.portal.view');

        $reseller = $request->user()->customer;

        if (! $reseller) {
            return view('reseller-portal.unlinked');
        }

        $homeCustomers = $reseller->homeCustomers()->withCount('tickets')->orderBy('name')->get();

        return view('reseller-portal.index', compact('reseller', 'homeCustomers'));
    }

    public function show(Request $request, Customer $customer)
    {
        $this->authorize('reseller.portal.view');

        $reseller = $request->user()->customer;

        // Scoped strictly to this reseller's own Home customers — never trust the route
        // param alone, a reseller must never be able to view another reseller's data by
        // guessing an id.
        abort_unless($reseller && $customer->reseller_id === $reseller->id, 404);

        $tickets = $customer->tickets()->latest('created_at')->limit(10)->get();

        return view('reseller-portal.show', compact('customer', 'tickets'));
    }
}
