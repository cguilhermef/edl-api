<?php

use Illuminate\Database\Seeder;

class SeedRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['Suporte', 'Atirador', 'Meio', 'Topo','Caçador'];
        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role,
            ]);
        }
    }
}
