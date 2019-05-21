<?php
/**
 * Created by PhpStorm.
 * User: guilh
 * Date: 21/05/2019
 * Time: 07:04
 */
namespace App\Services;

use App\Exceptions\ApplicationException;
use \App\Models\User;
use App\Models\UserSession;
use Laravel\Lumen\Http\Request;
use Ramsey\Uuid\Uuid;

class UserSessionService
{
    public function start(User $user, Request $request) {
        $session = UserSession::create([
           'user_id' => $user['id'],
           'access_token' => Uuid::uuid4(),
           'ip' => $request->getClientIp()
        ]);

        if(!$session) {
            throw new ApplicationException('Error on start a new session');
        }

        return $session;
    }
}
