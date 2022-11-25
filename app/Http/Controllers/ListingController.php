<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use ParsedownExtra;

class ListingController extends Controller
{
    public function index()
    {
        $listings = Listing::query()
            ->active()
            ->with('tags')
            ->filter(Request::only(['search', 'tag']))
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

    public function show(Listing $listing, Request $request)
    {
        return Inertia::render('Listings/Show', [
            'listing' => $listing->load('tags'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Listings/Create');
    }

    public function store(Request $request)
    {
        $validationArray = [
            'title' => 'required',
            'company' => 'required',
            'logo' => 'file|max:2048',
            'location' => 'required',
            'apply_link' => 'required|url',
            'content' => 'required',
            'payment_method_id' => 'required',
        ];

        if (! Auth::check()) {
            $validationArray = array_merge($validationArray, [
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:5',
                'name' => 'required',
            ]);
        }

        Request::validate($validationArray);

        // is a user signed in? if not, create one and authenticate
        $user = Auth::user();

        if (! $user) {
            $user = User::create([
                'name' => Request::input('name'),
                'email' => Request::input('email'),
                'password' => Hash::make(Request::input('password')),
            ]);

            $user->createAsStripeCustomer();

            Auth::login($user);
        }

        // process the payment and create the listing
        try {
            $amount = 9900; // $99.00 USD in cents
            if (Request::filled('is_highlighted')) {
                $amount += 1900;
            }

            $user->charge($amount, Request::input('payment_method_id'));

            $md = new ParsedownExtra();

            $listing = $user->listings()
                ->create([
                    'title' => Request::input('title'),
                    'slug' => Str::slug(Request::input('title')).'-'.rand(1111, 9999),
                    'company' => Request::input('company'),
                    'logo' => basename(Request::file('logo')->store('public')),
                    'location' => Request::input('location'),
                    'apply_link' => Request::input('apply_link'),
                    'content' => $md->text(Request::input('content')),
                    'is_highlighted' => Request::filled('is_highlighted'),
                    'is_active' => true,
                ]);

            foreach (explode(',', Request::input('tags')) as $requestTag) {
                $tag = Tag::firstOrCreate([
                    'slug' => Str::slug(trim($requestTag)),
                ], [
                    'name' => ucwords(trim($requestTag)),
                ]);

                $tag->listings()->attach($listing->id);
            }

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
