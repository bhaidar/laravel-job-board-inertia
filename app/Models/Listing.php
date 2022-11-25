<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Scope a query to only retrieve active listings
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', '%'.strtolower($search).'%')
                    ->orWhere('company', 'LIKE', '%'.strtolower($search).'%')
                    ->orWhere('location', 'LIKE', '%'.strtolower($search).'%')
                    ->orWhere('content', 'LIKE', '%'.strtolower($search).'%');
            });
        })->when($filters['tag'] ?? null, function ($query, $tag) {
            $query->where(function ($query) use ($tag) {
                $query->whereHas('tags', function ($query) use ($tag) {
                    $query->where('name', 'LIKE', $tag);
                });
            });
        });
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (new Carbon($value))->diffForHumans(),
        );
    }
}
