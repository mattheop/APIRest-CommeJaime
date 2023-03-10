<?php

use App\Application\Controllers\HomeController;
use App\Application\Middleware\JSONHeaderMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) {

    $app->get("/", [HomeController::class, 'index']);

    $app->group("/api/v1", function (RouteCollectorProxy $apiGroup) {

    })->addMiddleware(new JSONHeaderMiddleware());


    return $app;
};