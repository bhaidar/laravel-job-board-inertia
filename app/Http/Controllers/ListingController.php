<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Tag;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ListingController extends Controller
{
    public function index()
    {
        $listings = Listing::query()
            ->active()
            ->with('tags')
            ->filter(Request::only('search', 'tag'))
            ->latest()
            ->get();

        $tags = Tag::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Listings/Index', [
            // Return any query strings
            'filters' => Request::all('tag', 'search'),
            'listings' => $listings,
            'tags' => $tags,
        ]);
    }
}
