<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    //return view('welcome');
    $robot = new \App\Libraries\UptimeRobot('m777393915-b870b4f5c2344e73d3c246b2');
    $getMonitors = simplexml_load_string($robot->getMonitors());
    //$getMonitors = $robot->getMonitors();

    foreach($getMonitors->monitor as $monitor)
    {
        echo $monitor['status'];
    }
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
