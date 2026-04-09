<?php

namespace Database\Seeders;

use App\Models\Trailer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Test medewerker-account (alleen voor dev). Wachtwoord wordt ge-hashed via User mutator.
        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'role' => 'ADMIN',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 7 vaste aanhangwagens
        foreach (range(1, 7) as $i) {
            Trailer::firstOrCreate(
                ['code' => 'T' . $i],
                ['name' => 'Aanhangwagen ' . $i, 'is_active' => true]
            );
        }
    }
}
