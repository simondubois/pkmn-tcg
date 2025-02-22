<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Discovery;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DiscoverySeeder extends Seeder
{
    public function run(): void
    {
        Discovery::truncate();

        User::all()->map(
            fn (User $user): Collection => Card::inRandomOrder()->limit(rand(10, 100))->get()->map(
                fn (Card $card): Discovery => Discovery::create([
                    'card_id' => $card->id,
                    'user_id' => $user->id,
                    'quantity' => rand(1, 5),
                ])
            )
        );
    }
}
