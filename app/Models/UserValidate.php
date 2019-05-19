<?php
/**
 * Created by PhpStorm.
 * User: guilh
 * Date: 18/05/2019
 * Time: 23:08
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserValidate extends Model
{
    protected $fillable = [
        'user_id', 'token'
    ];

    protected $table = 'users_validates';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
