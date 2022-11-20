<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = Tag::factory(10)->create();

        User::factory(20)->create()->each(function ($user) use ($tags) {
            Listing::factory(rand(1, 4))->create([
                'user_id' => $user->id,
            ])->each(function ($listing) use ($tags) {
                $listing->tags()->attach($tags->random(2));
            });
        });
    }
}
