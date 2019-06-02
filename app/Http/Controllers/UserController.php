<?php
/**
 * Created by PhpStorm.
 * User: guilh
 * Date: 18/05/2019
 * Time: 17:33
 */

namespace App\Http\Controllers;


use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\Services\UserSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;
    private $userSessionService;

    public function __construct(UserService $userService, UserSessionService $userSessionService)
    {
        $this->userService = $userService;
        $this->userSessionService = $userSessionService;
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            return response('Email já cadastrado!', 400);
        }

        $user = $this->userService->register($request->all());

        return new UserResource($user);
    }

    public function validateEmail(Request $request) {
        $user = $request->user();
        $this->userService->sendUserEmailValidate($user);
    }

    public function activate(Request $request, string $token)
    {

        if ($this->userService->activate($token)) {
            return response(null, 204);
        }

        return response('Error on email validation', 400);
    }

    public function login(Request $request)
    {
        /** valida se foram enviados usuário e senha corretamente */
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        /** Verifica se o usuário existe e se a senha está correta */
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response('Usuário ou senha inválidos', 401);
        }

        $session = $this->userSessionService->start($user, $request);

        return [
            'access_token' => $session->access_token,
            'email_verified' => (bool)$user->valid,
            'account_verified' => (bool)$user->valid
        ];
    }

    public function registerAccount(Request $request)
    {
        return response('ok, register-account', 201);
    }

    public function confirmAccount(Request $request) {
        return response('ok, confirm-account', 201);
    }

    public function teste(Request $request) {
        $user = Auth::user();
        return response('ok!', 200);
    }
}
