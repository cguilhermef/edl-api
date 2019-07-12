<?php


namespace App\Models;


class Team extends Base
{
    protected $fillable = [
        'abbreviation',
        'name',
        'minRanking',
        'owner'
    ];

    protected $table = 'teams';

    protected $hidden = ['first_page_url'];

    public function user() {
        return $this->belongsTo(User::class, 'owner', 'id');
    }

    public function initialRanking() {
        return $this->belongsTo(Ranking::class, 'minRanking', 'id');
    }
}