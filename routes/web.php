<?php

use Illuminate\Support\Facades\Route;

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

//Route::get('/', function () {
//    return view('welcome');
//});

//Route::webhooks('webhook-receiving-url');

Route::get('/', [\App\Http\Controllers\WebhookTelegram::class, 'configTelegram']);
Route::post('/create-config', [\App\Http\Controllers\WebhookTelegram::class, 'createConfig']);
Route::post('webhook-test', [\App\Http\Controllers\WebhookTelegram::class, 'getWebhook']);
