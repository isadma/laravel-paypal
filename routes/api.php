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

Route::post("/paypal/create", "PaypalController@create")->name("paypal.create");
Route::post("/paypal/execute", "PaypalController@execute")->name("paypal.execute");
Route::post("/paypal/status", "PaypalController@status")->name("paypal.status");
