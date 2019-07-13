<?php

use Illuminate\Database\Seeder;

class SeedTiers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ['Ferro', 'Bronze', 'Prata', 'Ouro', 'Platina', 'Diamante'];
        $tiers = ['IRON', 'BRONZE', 'SILVER', 'GOLD', 'PLATINUM', 'DIAMOND'];
        $ranks = ['IV', 'III', 'II', 'I'];
        $level = 0;

        DB::table('rankings')->insert([
            'name' => 'Não ranqueado',
            'tier' => 'UNRANKED',
            'rank' => '',
            'level' => $level,
            'active' => true
        ]);
        $level++;

        for($i = 0; $i < count($tiers); $i++) {
            foreach ($ranks as $rank) {
                DB::table('rankings')->insert([
                    'name' => $names[$i],
                    'tier' => $tiers[$i],
                    'rank' => $rank,
                    'level' => $level,
                    'active' => true
                ]);
                $level++;
            }
        }
        DB::table('rankings')->insert([
            'name' => 'Mestre',
            'tier' => 'MASTER',
            'rank' => '',
            'level' => $level,
            'active' => true
        ]);
        $level++;
        DB::table('rankings')->insert([
            'name' => 'Grão-Mestre',
            'tier' => 'GRANDMASTER',
            'rank' => '',
            'level' => $level,
            'active' => true
        ]);
        $level++;
        DB::table('rankings')->insert([
            'name' => 'Desafiante',
            'tier' => 'CHALLENGER',
            'rank' => '',
            'level' => $level,
            'active' => true
        ]);
    }
}
