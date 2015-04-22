<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as' => 'home', 'uses' => 'CandidatesController@index']);

/**
 * Authentication & password reset
 */
Route::controllers([
    'auth' => 'AuthController',
    'password' => 'PasswordController'
]);

// Convenience routes
Route::get('admin', function() {
    return redirect()->to('/auth/admin');
});

/**
 * Categories
 */
Route::bind('categories', function ($slug) { return Category::where('slug', $slug)->first(); });
Route::resource('categories', 'CategoriesController');

/**
 * Candidates
 */
Route::bind('candidates', function ($slug) { return Candidate::where('slug', $slug)->first(); });
Route::resource('candidates', 'CandidatesController');

/**
 * Winners
 */
Route::resource('winners', 'WinnersController');

/**
 * Votes
 */
Route::resource('votes', 'VotesController', ['only' => ['store']]);

/**
 * Users
 */
Route::bind('users', function ($id) { return User::where('id', $id)->first(); });
Route::resource('users', 'UsersController', ['only' => ['index', 'create', 'store', 'show']]);

/**
 * Pages
 */
Route::bind('pages', function ($slug) { return Page::where('slug', $slug)->first(); });
Route::resource('pages', 'PagesController');

/**
 * Settings
 */
Route::bind('settings', function ($key) { return Setting::where('key', $key)->first(); });
Route::resource('settings', 'SettingsController', ['only' => ['index', 'edit', 'update']]);

/**
 * // @TODO: These should be middleware.
 * Voting Enabled Filter
 * This filter requires the 'Enable Voting' setting to be enabled for an
 * action to occur.
 */
Route::filter('voting_enabled', function () {
    $settings = App::make('SettingsRepository')->all();
    if (!$settings['enable_voting']) {
        return Redirect::to('/')->withFlashMessage('Sorry, voting is disabled!')
            ->with('flash_message_type', 'error');
    }
});

/**
 * // @TODO: These should be middleware.
 * Role Filter
 * The admin filter protects routes that should be not be accessible by everyone.
 */
Route::filter('role', function ($route, $request, $role) {
    if (Auth::guest() or !Auth::user()->hasRole($role)) {
        return Response::make('Unauthorized', 401);
    }
});
