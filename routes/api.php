<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\CartasController;

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

Route::middleware(['login-api-token', 'permisos']) -> prefix('usuarios') -> group(function(){
   
    Route::post('/login',[UsuariosController::class, 'login'])->withoutMiddleware(['login-api-token', 'permisos']);
    Route::put('/registro',[UsuariosController::class, 'registro'])->withoutMiddleware(['login-api-token', 'permisos']);;
    Route::get('/recoverPass',[UsuariosController::class, 'recoverPass'])->withoutMiddleware(['login-api-token', 'permisos']);
    Route::put('/altaCarta/{id}',[CartasController::class, 'altaCarta']);
    Route::put('/altaColeccion/{id}',[CartasController::class, 'altaColeccion']);
});
