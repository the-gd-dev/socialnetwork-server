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
// API route group
$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->group(['prefix' => 'utilities'], function () use ($router) {
        $router->get('languages', 'V1\UtilitiesController@getLanguages');
        $router->get('reactions', 'V1\UtilitiesController@getReactions');
        $router->get('privacies', 'V1\UtilitiesController@getPrivacies');
    });
    $router->group(['prefix' => 'photos'], function () use ($router) {
        $router->get('delete', 'V1\PhotosController@destroy');
        $router->get('/', 'V1\PhotosController@photos');
    });
    $router->group(['prefix' => 'friends'], function () use ($router) {
        $router->get('requests', 'V1\FriendsController@friendRequests');
        $router->post('add', 'V1\FriendsController@addFriend');
        $router->post('remove', 'V1\FriendsController@removeFriendRequest');
        $router->post('confirm', 'V1\FriendsController@confirmFriendRequest');
        $router->get('/', 'V1\FriendsController@friends');
    });
    $router->group(['prefix' => 'comments'], function () use ($router) {
        $router->get('all', 'V1\CommentsController@index');
        $router->post('add', 'V1\CommentsController@store');
        $router->post('remove', 'V1\CommentsController@destroy');
    });
    // Matches "/api/register
    $router->post('register', 'V1\AuthController@register');
    // Matches "/api/login
    $router->post('login', 'V1\AuthController@login');

    // Matches "/api/profile
    $router->get('profile', 'V1\UserController@profile');
    $router->get('users/{id}', 'V1\UserController@singleUser');

    //get one user by id
    $router->get('people', 'V1\PeopleController@randomPeople');
    $router->get('remove-person', 'V1\PeopleController@removePerson');
    

    // Posts -> APIs
    $router->group(['prefix' => 'posts'], function () use ($router) {
        $router->get('/', 'V1\PostsController@posts');
        $router->get('/{postId}', 'V1\PostsController@getPost');
        $router->post('create', 'V1\PostsController@create');
        $router->post('delete', 'V1\PostsController@destroy');
        $router->post('reaction', 'V1\PostsController@setReaction');
        $router->post('privacy', 'V1\PostsController@setPrivacy');
    });
    
});
