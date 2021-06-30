<?php

namespace Backpack\CRUD\Tests\Config\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $now = \Carbon\Carbon::now();

        DB::table('users')->insert([[
            'id'             => 1,
            'name'           => $faker->name,
            'email'          => $faker->unique()->safeEmail,
            'password'       => bcrypt('secret'),
            'remember_token' => Str::random(10),
            'created_at'     => $now,
            'updated_at'     => $now,
        ]]);

        DB::table('users')->insert([[
            'id'             => 2,
            'name'           => $faker->name,
            'email'          => $faker->unique()->safeEmail,
            'password'       => bcrypt('secret'),
            'remember_token' => Str::random(10),
            'created_at'     => $now,
            'updated_at'     => $now,
        ]]);
    }
}
