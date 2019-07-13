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

use \App\Models\Roles;

$router->post('register', "UserController@register");
$router->get('activate/{token}', [
    "uses" => "UserController@activate",
    "as" => "user.activate"
]);
$router->post('login', "UserController@login");
$router->post('forgot-password', "UserController@forgot");
$router->post('recovery', [
    'uses' => 'UserController@recovery',
    'as' => 'user.recovery'
]);
$router->post('register-account', [
    'uses' => 'UserController@registerAccount',
    'middlware' => ['auth', 'valid_email']
]);
$router->post('confirm-account', "UserController@confirmAccount");
$router->post('register-summoner', [
    'middlware'=> [ 'auth', 'valid_email'],
    'uses' => 'UserController@registerSummoner'
]);
$router->get('teste', [
    'uses' => 'UserController@teste'
]);

/** Teams */

$router->post('teams/', [
    'uses' => 'TeamController@create',
    'middlware' => [ 'auth', 'valid_email', 'confirmed_account']
]);
$router->put('teams/{teamId}', [
    'uses' => 'TeamController@update',
    'middlware' => [ 'auth', 'valid_email', 'confirmed_account']
]);
$router->delete('teams/{teamId}', [
    'uses' => 'TeamController@destroy',
    'middlware' => [ 'auth', 'valid_email', 'confirmed_account']
]);

$router->get('teams[/{page},/{size}]', 'TeamController@index');
$router->get('teams/{teamId}', 'TeamController@show');

/** outros */
$router->get('rankings', 'RankingsController@index');
$router->get('roles', function () {
    return Roles::orderBy('name')->get();
});