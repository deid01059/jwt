<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::post('auth/login', [AuthController::class,'login']);
// 미들웨어 설정 kernel.php에 등록
Route::get('auth',[AuthController::class,'reisstoken']);
Route::middleware('my.token.auth')->get('boards', function(){
	return response()->json([
		'code' => '0'
		,'msg' => '인증된 유저 입니다.'
	]);
});