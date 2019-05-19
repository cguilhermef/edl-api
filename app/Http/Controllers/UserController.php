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
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $this->validate($request, [
           'name' => 'required|string|max:255',
           'email' => 'required|email',
           'password' => 'required'
        ]);

        $user = User::where('email', $request->input('email'));

        if ($user) {
            return response('Email já cadastrado!', 400);
        }

        $user = $this->userService->register($request->all());

        return new UserResource($user);
    }

    public function activate(Request $request, string $token) {

        if ($this->userService->activate($token) ){
            return response(null, 204);
        }

        return response('Error on email validation', 400);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        dd($user->password);
    }
}
