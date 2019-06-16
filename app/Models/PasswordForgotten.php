<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PasswordForgotten extends Model
{
    protected $fillable = ['user_id', 'token'];
    protected $table = 'passwords_forgotten';

    public function user() {
        return $this->belongsTo(User::class);
    }
}