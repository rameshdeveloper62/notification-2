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

Route::get('/', function () {
    return view('home');
});

Route::get('mail','HomeController@mailNotification')->name('mail');
Route::get('sms','HomeController@smsNotification')->name('sms');
Route::get('slack','HomeController@slackNotification')->name('slack');
Route::get('database','HomeController@databaseNotification')->name('database');
Route::get('brodcast','HomeController@brodcastNotification')->name('brodcast');

Route::post('change-status','HomeController@changeStatus')->name('change-status');
Route::get('read-notification/{notificationId}/{type?}','HomeController@readNotification')->name('read-notification');
Route::get('delete-notification/{notificationId}','HomeController@deleteNotification')->name('delete-notification');

Auth::routes();

//preview email (it is working only in first method inside toMail())
Route::get('email', function () {
    return (new App\Notifications\OrderStatusMail('Delivered'))
                ->toMail('test1@gmail.com');
});

Route::get('order-status', function(){
    broadcast(new \App\Events\OrderStatusEvent);
});