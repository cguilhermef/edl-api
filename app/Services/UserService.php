<?php
/**
 * Created by PhpStorm.
 * User: guilh
 * Date: 18/05/2019
 * Time: 17:54
 */

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Mail\PasswordForgottenMail;
use App\Mail\UserEmailValidate;
use App\Models\PasswordForgotten;
use App\Models\User;
use App\Models\UserValidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            'password' => Hash::make($data['password'])
        ]);

        if(!$user) {
            throw new ApplicationException('Error on register user', 422);
        }

        $this->sendUserEmailValidate($user);

        return $user;

    }

    public function sendUserEmailValidate($user) {
        $userValidate = UserValidate::create([
            'user_id' => $user->id,
            'token'=> Str::random(36)
        ]);

        if($userValidate) {
            Mail::send(new UserEmailValidate($user, $userValidate->token));
        }
    }

    public function sendEmailPasswordForgotten($user) {
        $passwordForgot = PasswordForgotten::create([
            'user_id' => $user->id,
            'token' => Str::random(36)
        ]);
        if ($passwordForgot) {
            Mail::send(new PasswordForgottenMail($user, $passwordForgot->token));
        }
    }

    public function activate(string $token) {

        $userActivate = UserValidate::with(['user'])
            ->where('token', '=', $token)
            ->where('created_at', '>=', (new Carbon('now'))->subMinute(10))
            ->first();

        if ($userActivate && $userActivate->user->valid === 0) {

            return DB::table('users')->update(['valid' => true]);
        }

        return false;
    }

}
