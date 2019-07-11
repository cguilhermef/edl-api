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
        $names = ['Ferro', 'Bronze', 'Prata', 'Ouro', 'Platina', 'Diamante', 'Meste', 'Grão-Mestre', 'Desafiante'];
        $tiers = ['IRON', 'BRONZE', 'SILVER', 'GOLD', 'PLATINUM', 'DIAMOND', 'MASTER', 'GRANDMASTER', 'CHALLENGER'];
        $ranks = ['IV', 'III', 'II', 'I'];
        $level = 0;

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
    }
}
