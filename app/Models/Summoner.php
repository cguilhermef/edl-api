<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Summoner extends Model
{
    protected $fillable = [
        'summonerId',
        'accountId',
        'puuid',
        'user_id',
        'name',
        'summonerLevel',
        'revisionDate',
        'profileIconId'
    ];
    
    protected $table = 'summoners';

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
