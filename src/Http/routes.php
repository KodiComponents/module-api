<?php

Route::group(['as' => 'api.', 'middleware' => ['api', 'backend']], function () {
    RouteAPI::post('refresh.key', ['as' => 'refresh.key', 'uses' => 'API\ApiTokensController@postRefresh']);
    RouteAPI::get('keys', ['as' => 'keys.list', 'uses' => 'API\ApiTokensController@getKeys']);
    RouteAPI::put('key', ['as' => 'key.put', 'uses' => 'API\ApiTokensController@putKey']);
    RouteAPI::delete('key', ['as' => 'key.delete', 'uses' => 'API\ApiTokensController@deleteKey']);
});
