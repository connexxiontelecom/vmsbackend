<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


// Authenticated API route group
$router->group(['middleware' => ['jwt.auth'], 'prefix'=>'api/auth' ], function () use ($router) {
    $router->post('newvisitor', 'VisitorsController@register');
    $router->get('visitors', 'VisitorsController@getVisitors');
    $router->get('morevisitors', 'VisitorsController@getMoreVisitors');
    $router->get('allvisitors', 'VisitorsController@getAllVisitors');
    $router->post('newvisit', 'VisitorsController@createVisit');
    $router->get('visitorhistory', 'VisitorsController@getVisits');
});

$router->group(['prefix'=>'api'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
});
