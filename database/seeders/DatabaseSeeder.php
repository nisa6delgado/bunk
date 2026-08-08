<?php

namespace Database\Seeders;

use App\Models\Setting;
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
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => '123',
        ]);

        Setting::create([
            'key' => 'name',
            'value' => 'Inventario',
        ]);

        Setting::create([
            'key' => 'color',
            'value' => '#E97829',
        ]);

        Setting::create([
            'key' => 'favicon',
            'value' => 'favicon.png',
        ]);
    }
}
