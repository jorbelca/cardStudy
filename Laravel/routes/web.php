<?php

use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRecordController;
use App\Models\Question;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/questions', [TestController::class, 'testOrm']);


// RUTAS DE LA API
// Rutas prueba
// Route::get('/usuario/pruebas', [UserController::class, 'pruebas']);
// Route::get('/question/pruebas', [QuestionController::class, 'pruebas']);
// Route::get('/user_record/pruebas', [UserRecordController::class, 'pruebas']);
// Route::get('/usuarios', [UserController::class, 'usuarios']);

// Rutas Controlador de Usuarios
Route::post('/api/login', [UserController::class, 'login']);
Route::post("/api/register", [UserController::class, 'register']);
Route::put("/api/user/update", [UserController::class, 'update']);
Route::get("/api/user/detail/{id}", [UserController::class, 'detail']);


// Rutas del controlador de topics
Route::resource('/api/topics', TopicController::class);


// Rutas del controlador de Preguntas
Route::resource('/api/questions', QuestionController::class);
Route::get('/api/questions/topic/{id}', [QuestionController::class, 'getQuestionsByTopic']);
Route::get('/api/questions/creator/{id}', [QuestionController::class, 'getQuestionsByCreator']);
