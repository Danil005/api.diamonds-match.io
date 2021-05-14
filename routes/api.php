<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->middleware('api')->namespace('App\Http\Controllers\Api\v1')->group(function() {
    Route::prefix('auth')->group(function () {
        Route::post('auth.login', 'AuthController@login')->name('auth.login');

        Route::middleware('auth:api')->group(function() {
            Route::put('auth.create', 'AuthController@create')->name('auth.create');
            Route::get('auth.me', 'AuthController@getMe')->name('auth.me');
        });
    });

    Route::prefix('employee')->middleware('auth:api')->group(function() {
        Route::get('employee.get', 'EmployeeController@get')->name('employee.get');
        Route::post('employee.update', 'EmployeeController@update')->name('employee.update');
        Route::post('employee.newPassword', 'EmployeeController@newPassword')->name('employee.newPassword');
        Route::delete('employee.archive', 'EmployeeController@archive')->name('employee.archive');
        Route::post('employee.unarchive', 'EmployeeController@unarchive')->name('employee.unarchive');
    });

    Route::prefix('questionnaire')->group(function() {
        Route::put('questionnaire.create', 'QuestionnaireController@create');
        Route::put('questionnaire.createFromSite', 'QuestionnaireController@createFromSite');

        Route::middleware('auth:api')->group(function() {
            Route::get('questionnaire.view', 'QuestionnaireController@view');

            Route::post('questionnaire.uploadPhoto', 'QuestionnaireController@uploadPhoto');
            Route::delete('questionnaire.deletePhoto', 'QuestionnaireController@deletePhoto');

            Route::post('questionnaire.uploadFile', 'QuestionnaireController@uploadFile');
            Route::post('questionnaire.downloadFile', 'QuestionnaireController@openFile');
            Route::delete('questionnaire.deleteFile', 'QuestionnaireController@deleteFile');

            Route::post('questionnaire.makeDate', 'QuestionnaireController@makeDate');

            Route::get('questionnaire.get', 'QuestionnaireController@get');
            Route::get('questionnaire.getHistory', 'QuestionnaireController@getHistory');
            Route::post('questionnaire.addHistory', 'QuestionnaireController@addHistory');
            Route::delete('questionnaire.removeHistory', 'QuestionnaireController@removeHistory');

            Route::get('questionnaire.getMatch', 'QuestionnaireController@getMatch');
            Route::get('questionnaire.getMakeDate', 'QuestionnaireController@getMakeDate');
            Route::get('questionnaire.getAppointedDate', 'QuestionnaireController@getAppointedDate');

            Route::post('questionnaire.setStatus', 'QuestionnaireController@setStatus');
        });
    });

    Route::prefix('applications')->group(function() {
        Route::put('applications.createFromOthers', 'ApplicationsController@create');

        Route::middleware('auth:api')->group(function() {
            Route::put('applications.create', 'ApplicationsController@create');
            Route::get('applications.get', 'ApplicationsController@get');
            Route::get('applications.view', 'ApplicationsController@view');
            Route::post('applications.change', 'ApplicationsController@change');
            Route::post('applications.startWork', 'ApplicationsController@startWork');
            Route::post('applications.update', 'ApplicationsController@update');
            Route::delete('applications.delete', 'ApplicationsController@delete');
            Route::post('applications.unarchive', 'ApplicationsController@unarchive');
        });
    });

    Route::prefix('analytics')->group(function() {
        Route::middleware('auth:api')->group(function() {
            Route::get('analytics.get', 'AnalyticsController@get');
        });
    });

    Route::prefix('utils')->group(function() {
        Route::get('utils.cities', 'UtilsController@getCities');
        Route::get('utils.countries', 'UtilsController@getCountry');
        Route::get('utils.languages', 'UtilsController@getLanguage');
    });
});
