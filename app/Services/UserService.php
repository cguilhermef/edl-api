<?php
/**
 * Created by PhpStorm.
 * User: guilh
 * Date: 18/05/2019
 * Time: 17:54
 */

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Mail\UserEmailValidate;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'valid_token' => Str::random(32)
        ]);

        if(!$user) {
            throw new ApplicationException('Error on register user', 422);
        }

        Mail::send(new UserEmailValidate($user));

        return $user;

    }

}
