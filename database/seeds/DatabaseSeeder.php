<?php

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
        $this->call(BouncerSeeder::class);
        // $this->call(LaratrustSeeder::class);
        // $this->call(FileGroupSeeder::class);
    }
}
