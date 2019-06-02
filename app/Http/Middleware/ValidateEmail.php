<?php

namespace App\Http\Middleware;

use App\Services\UserService;
use Closure;

class ValidateEmail
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user->valid) {
            $this->userService->sendUserEmailValidate($user);
            return response('Email não verificado!', 401);
        }
        return $next($request);
    }
}
