<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', ['as' => 'home', 'uses' => 'CandidatesController@index']);

/**
 * Categories
 */
Route::bind('categories', function($slug) {
  return Category::whereSlug($slug)->first();
});

Route::resource('categories', 'CategoriesController');

/**
 * Candidates
 */
Route::bind('candidates', function($slug) {
  return Candidate::whereSlug($slug)->first();
});

Route::resource('candidates', 'CandidatesController');

/**
 */

/**
 * Users
 */
Route::resource('users', 'UsersController', ['only' => ['create', 'store']]);

/**
 * Sessions
 */
Route::resource('sessions', 'SessionsController', ['only' => ['create', 'store', 'destroy']]);
Route::get('login', ['as' => 'login', 'uses' => 'SessionsController@create']);
Route::get('logout', ['as' => 'logout', 'uses' => 'SessionsController@destroy']);

