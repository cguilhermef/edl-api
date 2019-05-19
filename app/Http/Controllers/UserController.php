<?php
/**
 * Created by PhpStorm.
 * User: guilh
 * Date: 18/05/2019
 * Time: 17:33
 */
namespace App\Http\Controllers;


use App\Http\Resources\UserResource;
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

    public function register(Request $request) {

        $this->validate($request, [
           'name' => 'required|string|max:255',
           'email' => 'required|email',
           'password' => 'required'
        ]);

        $user = $this->userService->register($request->all());

        return new UserResource($user);
    }
}
