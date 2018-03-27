<?php
namespace Bizhost\PayourbillOrders\Database\Seeds;

use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    public function run()
    {
        DB::table('websaanova_demo_items')->insert([
            'slug' => 'test',
            'name' => 'Test',
            'description' => 'My first item test.',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}

