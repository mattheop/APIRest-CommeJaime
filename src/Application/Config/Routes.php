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
        });
    })->addMiddleware(new JSONHeaderMiddleware());


    return $app;
};