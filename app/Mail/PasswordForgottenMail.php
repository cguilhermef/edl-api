<?php
/**
 * Created by PhpStorm.
 * User: guilh
 * Date: 18/05/2019
 * Time: 20:59
 */

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;

class PasswordForgottenMail extends Mailable
{
    private $user;
    private $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->to($user->email, $user->name);
        $this->subject('Recuperar senha');
    }

    public function build()
    {
        return $this->view('mail.password-forgotten-email', [
            'user' => $this->user,
            'url_activate' => route('user.recovery', [ 'token' => $this->token])
        ]);
    }
}
