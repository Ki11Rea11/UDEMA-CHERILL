<?php

use Laminas\Diactoros\Response\RedirectResponse;
use MiladRahimi\PhpRouter\Router;
use Src\Controllers\AuthController;
use Src\Controllers\MainController;
use Src\Controllers\UserController;
use Src\middleware\AdminMiddleware;
use Src\middleware\AuthMiddleware;

require_once 'vendor/autoload.php';

ORM::configure('mysql:host=database;dbname=docker');
ORM::configure('username', 'docker');
ORM::configure('password', 'docker');

session_start();


$router = Router::create();
$router->setupView('Views');

$router->get('/',[MainController::class,'mainPage']);
$router->get('/register',[MainController::class,'registrationPage']);
$router->get('/login',[MainController::class,'loginPage']);
$router->post('/register',[AuthController::class,'registration']);
$router->post('/login',[AuthController::class,'login']);
$router->get('/courses_list',[MainController::class,'coursesPage']);
$router->get('/course_detail/{course_id}',[MainController::class,'course_detailPage']);

$router->group(
    ['middleware' =>[AuthMiddleware::class]],
    function (Router $router){
        $router->post('/comment/{course_id}',[UserController::class,'comment']);
        $router->get('/user/profile',[MainController::class,'userprofilePage']);
        $router->post('/user/update',[UserController::class,'updateUserInfo']);
    $router->group(['middleware' => [AdminMiddleware::class]],
    function (Router $router){

    });
    $router->get('/clear-session',function (){
        unset($_SESSION['user_id']);
        return new RedirectResponse('/');
    });
});



$router->dispatch();