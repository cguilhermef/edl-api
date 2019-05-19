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

class UserEmailValidate extends Mailable
{
    private $user;
    private $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->to($user->email, $user->name);
        $this->subject('Confirmação de e-mail');
    }

    public function build()
    {
        return $this->view('mail.user-validate-email', [
            'user' => $this->user,
            'url_activate' => route('user.activate', [ 'token' => $this->token])
        ]);
    }
}
