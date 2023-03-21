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
            $postGroup->get("", [PostController::class, 'fetchAll'])->setName("posts.fetchAll");
            $postGroup->get("/{id}", [PostController::class, 'fetch'])->setName("posts.fetch");
            $postGroup->delete("/{id}", [PostController::class, 'delete'])->setName("posts.delete");
            $postGroup->post("", [PostController::class, 'post'])->setName("posts.create");
            $postGroup->post("/{id_post}/like", [PostController::class, 'postLike'])->setName("posts.like");
            $postGroup->delete("/{id_post}/like", [PostController::class, 'deleteLike'])->setName("posts.removelike");
        });
    })->addMiddleware(new JSONHeaderMiddleware());


    return $app;
};