<?php


namespace App\Models;


class AccountConfirmation extends Base
{
    protected $fillable = [
        'icon_id', 'user_id'
    ];

    public function user()
    {
        $this->belongsTo(User::class);
    }
}