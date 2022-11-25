<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListingClickController extends Controller
{
    public function store(Listing $listing, Request $request)
    {
        $listing->clicks()->create([
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
        ]);

        // redirect to external resource with Inertia
        return Inertia::location($listing->apply_link);
    }
}
