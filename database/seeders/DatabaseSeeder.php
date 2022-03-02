<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(LanguagesTableSeeder::class);
        // $this->call(ReactionsTableSeeder::class);
        // $this->call(PrivaciesTableSeeder::class);
        // $this->call(UsersTableSeeder::class);
        $this->call(UserMetaTableSeeder::class);
        // $this->call(PostsTableSeeder::class);
    }
}
