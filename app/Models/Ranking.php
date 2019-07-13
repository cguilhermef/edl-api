<?php


namespace App\Models;


class Ranking extends Base
{

    protected $table = 'rankings';

    protected $fillable = [
        'name',
        'tier',
        'rank',
        'level'
    ];

    protected $hidden = [ 'created_at', 'updated_at', 'active'];

    public function teams() {
        return $this->hasMany(Team::class, 'minRanking', 'id');
    }
}