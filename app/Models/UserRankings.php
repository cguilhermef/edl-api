<?php


namespace App\Models;


class UserRankings extends Base
{

    protected $fillable = [
        'userId',
        'rankingId',
        'id',
        'queueType'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}