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
    'middleware' => ['auth', 'valid_email']
]);
$router->post('confirm-account', "UserController@confirmAccount");
$router->post('register-summoner', [
    'middleware' => ['auth', 'valid_email'],
    'uses' => 'UserController@registerSummoner'
]);
/** Teams */

$router->post('teams/', [
    'uses' => 'TeamController@create',
    'middleware' => ['auth', 'valid_email', 'confirmed_account']
]);
$router->put('teams/{teamId}', [
    'uses' => 'TeamController@update',
    'middleware' => ['auth', 'valid_email', 'confirmed_account']
]);
$router->delete('teams/{teamId}', [
    'uses' => 'TeamController@destroy',
    'middleware' => ['auth', 'valid_email', 'confirmed_account']
]);

$router->get('teams[/{page},/{size}]', 'TeamController@index');
$router->get('teams/{teamId}', 'TeamController@show');

/** Vacancies */
$router->get('vacancies[/{page},/{size}]', 'VacancyController@index');

$router->post('vacancies/', [
    'uses' => 'VacancyController@store',
    'middleware' => ['auth', 'valid_email', 'confirmed_account']
]);

$router->delete('vacancies/{vacancyId}', [
    'uses' => 'VacancyController@destroy',
    'middleware' => ['auth', 'valid_email', 'confirmed_account']
]);

/** outros */
$router->get('rankings', 'RankingsController@index');
$router->get('roles', function () {
    return Roles::orderBy('name')->get();
});