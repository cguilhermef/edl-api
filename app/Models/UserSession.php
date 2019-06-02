<?php
/**
 * Created by PhpStorm.
 * User: guilh
 * Date: 21/05/2019
 * Time: 07:02
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'user_id', 'access_token', 'ip'
    ];

    protected $table = 'user_sessions';

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
