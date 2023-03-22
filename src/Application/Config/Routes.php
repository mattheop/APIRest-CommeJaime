<?php

use App\Application\Controllers\AuthController;
use App\Application\Controllers\HomeController;
use App\Application\Controllers\PostController;
use App\Application\Middleware\AuthMiddleware;
use App\Application\Middleware\JSONHeaderMiddleware;
use App\Application\Middleware\MinimumRoleMiddleware;
use App\Domain\Users\Roles;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) {

    $app->get("/", [HomeController::class, 'index']);

    $app->group("/api/v1", function (RouteCollectorProxy $apiGroup) {
        $apiGroup->group("/posts", function (RouteCollectorProxy $postGroup) {
            $postGroup->get("", [PostController::class, 'fetchAll'])
                ->setName("posts.fetchAll");

            $postGroup->group("", function (RouteCollectorProxy $authedRoutes){
                $authedRoutes->get("/{id}", [PostController::class, 'fetch'])
                    ->setName("posts.fetch");

                $authedRoutes->delete("/{id}", [PostController::class, 'delete'])
                    ->setName("posts.delete");

                $authedRoutes->post("", [PostController::class, 'post'])
                    ->setName("posts.create");

                $authedRoutes->post("/{id_post}/like", [PostController::class, 'postLike'])
                    ->setName("posts.like");

                $authedRoutes->delete("/{id_post}/like", [PostController::class, 'deleteLike'])
                    ->setName("posts.removelike");
            })->addMiddleware(new MinimumRoleMiddleware(Roles::ROLE_PUBLISHER));
        });
    })->addMiddleware(new JSONHeaderMiddleware())->addMiddleware(new AuthMiddleware());

    $app->post("/api/v1/auth", [AuthController::class, 'fetch'])
        ->addMiddleware(new JSONHeaderMiddleware());

    return $app;
};