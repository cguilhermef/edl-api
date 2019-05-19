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

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->to($user->email, $user->name);
        $this->subject('Confirmação de e-mail');
    }

    public function build()
    {
        return $this->view('mail.user-validate-email', [ 'user' => $this->user ]);
    }
}
