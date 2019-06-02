<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
/** cadastro de usuário */
$router->post('register', "UserController@register");
/** ativação de usuário - validação de email */
$router->get('activate/{token}', [
    "uses" => "UserController@activate",
    "as" => "user.activate"
]);
$router->post('login', "UserController@login");
$router->post('register-account', "UserController@registerAccount");
$router->post('confirm-account', "UserController@confirmAccount");
$router->get('teste', [
    'middleware' => ['auth', 'valid_email'],
    'uses' => 'UserController@teste'
]);
