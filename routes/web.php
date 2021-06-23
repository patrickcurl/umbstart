<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', 'Front\\HomeController@index')->name('front.home');
Route::get('/')->name('dashboard')->uses('DashboardController@index')->middleware('auth');

Route::get('login')->name('login')->uses('Auth\LoginController@showLoginForm')->middleware('guest');
Route::post('login')->name('login.attempt')->uses('Auth\LoginController@login')->middleware('guest');
Route::post('logout')->name('logout')->uses('Auth\LoginController@logout');
// Route::get('register')->name('register')->uses('Auth\RegisterController@showRegistrationForm')->middleware('guest');
// Route::post('register')->name('register')->uses('Auth\RegisterController@register')->middleware('guest');

// Route::get('/')->name('dashboard')->uses('DashboardController')->middleware('auth');
Route::middleware(['auth', 'role:superadmin'])
    ->namespace('\App\Http\Actions\Impersonate')
    ->prefix('impersonate')
    ->as('impersonate.')
    ->group(function () {
        Route::post('stop', 'Stop')->name('stop');
        Route::post('start', 'Start')->name('start');
    });
Route::middleware(['remember', 'auth'])
    ->namespace('\App\Http\Actions\Search')
    ->prefix('search')
    ->as('search.')
    ->group(function () {
        Route::get('teams', 'Teams')->name('teams');
        Route::get('tags', 'Tags')->name('tags');
        Route::get('projects', 'Projects')->name('projects');
        Route::get('mailboxes', 'Mailboxes')->name('mailboxes');
        Route::get('roles', 'Roles')->name('roles');
        Route::get('permissions', 'Permissions')->name('permissions');
        Route::get('messages', 'Messages')->name('messages');
    });

Route::middleware(['remember', 'auth'])
    ->namespace('\App\Http\Actions\Mailboxes')
    ->prefix('mailboxes')
    ->as('mailboxes.')
    ->group(function () {
        Route::get('/', 'Index')->name('index');
        Route::get('/create', 'Create')->name('create');
        Route::post('/store', 'Store')->name('store');
        Route::get('/{mailbox}/edit', 'Edit')->name('edit');
        Route::post('/{mailbox}/fetch', 'Fetch')->name('fetch');
    });
Route::middleware(['remember', 'auth'])
    ->namespace('\App\Http\Actions\Images')
    ->prefix('images')
    ->as('images.')
    ->group(function () {
        Route::get('/{kw?}', 'Index')->name('index');
        Route::post('/store', 'Store')->name('store');
        Route::namespace('\App\Http\Actions\Images\Tags')
            ->prefix('tag')
            ->as('tag.')
            ->group(function () {
                Route::post('/{media}/refresh', 'Refresh')->name('refresh');
                Route::post('/{media}', 'Store')->name('add');
                // Route::get('/{search}', 'Search')->name('search');
                Route::delete('/{tag}', 'Destroy')->name('destroy');
            });
    });
Route::middleware(['remember', 'auth'])
    ->namespace('\App\Http\Actions\Users')
    ->prefix('users')
    ->as('users.')
    ->group(function () {
        Route::get('/{search}', 'Search')->name('search');
    });
Route::middleware(['remember', 'auth'])
    ->namespace('\App\Http\Actions\Teams')
    ->prefix('teams')
    ->as('teams.')
    ->group(function () {
        Route::put('/restore', 'Restore')->name('restore');
        Route::get('/create', 'Create')->name('create');
        Route::get('/', 'Index')->name('index');
        Route::post('/', 'Store')->name('store');
        Route::delete('/', 'Destroy')->name('destroy');
        Route::get('/{team}/edit', 'Edit')->name('edit');
        Route::put('/{team}', 'Update')->name('update');
    });
// Route::middleware(['auth:api'])
//     ->namespace('\App\Http\Actions\Teams')
//     ->prefix('teams')
//     ->as('teams')
//     ->group(function () {
//         Route::get('/', 'Index');
//     });
Route::get('users')->name('users')->uses('UsersController@index')->middleware('remember', 'auth');
Route::get('users/create')->name('users.create')->uses('UsersController@create')->middleware('auth');
Route::post('users')->name('users.store')->uses('UsersController@store')->middleware('auth');
Route::get('users/{user}/edit')->name('users.edit')->uses('UsersController@edit')->middleware('auth');
Route::put('users/{user}')->name('users.update')->uses('UsersController@update')->middleware('auth');
Route::delete('users/{user}')->name('users.destroy')->uses('UsersController@destroy')->middleware('auth');
Route::put('users/{user}/restore')->name('users.restore')->uses('UsersController@restore')->middleware('auth');

// Route::get('/media/{path}', 'MediaController@show')->where('path', '.*');


// Route::get('teams')->name('teams')->uses('\Actions\Teams\Index')->middleware('remember', 'auth');
// Route::get('teams/create')->name('teams.create')->uses('TeamsController@create')->middleware('auth');
// Route::post('teams')->name('teams.store')->uses('TeamsController@store')->middleware('auth');
// Route::get('teams/{team}/edit')->name('teams.edit')->uses('TeamsController@edit')->middleware('auth');
// Route::put('teams/{team}')->name('teams.update')->uses('TeamsController@update')->middleware('auth');
// Route::delete('teams/{team}')->name('teams.destroy')->uses('TeamsController@destroy')->middleware('auth');
// Route::put('teams/{team}/restore')->name('teams.restore')->uses('TeamsController@restore')->middleware('auth');

Route::get('reports')->name('reports')->uses('ReportsController@index')->middleware('auth');
// Auth::routes();


// NOTE:
// remove the demo middleware before you start on a project, this middleware if only
// for demo purpose to prevent viewers to modify data on a live demo site

// admin
// Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {
//     // single page
//     Route::get('/', 'SinglePageController@displaySPA')->name('admin.spa');

//     // resource routes
//     // Route::resource('users', 'UserController');
//     // Route::resource('roles', 'RoleController');
//     // Route::resource('permissions', 'PermissionController');
// });
