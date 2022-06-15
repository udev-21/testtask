<?php

use App\Http\Controllers\API\PostContoller;
use App\Http\Controllers\API\SubscribeContoller;
use App\Http\Controllers\API\WebsiteController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/subscribe/{website}', [SubscribeContoller::class, 'subscribe']);

Route::get('/subscribers', [SubscribeContoller::class, 'all']);
Route::get('/posts/queue', [SubscribeContoller::class, 'queue']);

Route::resource('/websites', WebsiteController::class );
Route::resource('/posts', PostContoller::class);
