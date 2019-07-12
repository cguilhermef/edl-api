<?php


namespace App\Http\Middleware;


use App\Services\UserService;
use Closure;

class ValidateAccount
{
//    private $userService;
//
//    public function __construct(UserService $userService)
//    {
//        $this->userService = $userService;
//    }

    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user->confirmed) {
            return response('Usuário não possui um perfil válido vinculado!', 400);
        }
        return $next($request);
    }
}