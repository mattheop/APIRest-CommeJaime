<?php

use App\Application\Controllers\HomeController;
use App\Application\Controllers\PostController;
use App\Application\Middleware\JSONHeaderMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) {

    $app->get("/", [HomeController::class, 'index']);

    $app->group("/api/v1", function (RouteCollectorProxy $apiGroup) {
        $apiGroup->group("/posts", function (RouteCollectorProxy $postGroup) {
            $postGroup->get("", [PostController::class, 'fetchAll']);
            $postGroup->get("/{id}", [PostController::class, 'fetch']);
            $postGroup->delete("/{id}", [PostController::class, 'delete']);
            $postGroup->post("", [PostController::class, 'post']);
            $postGroup->post("/{id_post}/like", [PostController::class, 'postLike']);
            $postGroup->delete("/{id_post}/like", [PostController::class, 'deleteLike']);
        });
    })->addMiddleware(new JSONHeaderMiddleware());


    return $app;
};