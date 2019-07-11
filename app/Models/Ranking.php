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

}