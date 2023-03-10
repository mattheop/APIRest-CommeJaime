<?php

namespace App\Application;

use App\Application\Middleware\ErrorRenderer;
use App\Application\Middleware\TrailingSlashMiddleware;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteParser;

require_once __DIR__ . '/../../vendor/autoload.php';
$baseDir = __DIR__ . '/../../';

class Application
{
    private static ?self $instance = null;

    private App $app;

    private function __construct()
    {
        $this->app = AppFactory::create();

        $this->registerErrorHandler();
        $this->registerMiddlewares();
        $this->registerRoutes();
    }

    public function getApp(): App{
        return $this->app;
    }

    public function getRouteParser(): RouteParser
    {
        return $this->app->getRouteCollector()->getRouteParser();
    }

    private function registerErrorHandler(): void
    {
        $errorMiddleware = $this->app->addErrorMiddleware(true, true, true);
        $errorHandler = $errorMiddleware->getDefaultErrorHandler();
        $errorHandler->forceContentType('application/json');
        $errorHandler->registerErrorRenderer('application/json', ErrorRenderer::class);
    }

    private function registerMiddlewares(): void
    {
        $this->app->addRoutingMiddleware();
        $this->app->add(new TrailingSlashMiddleware());
    }

    private function registerRoutes(): void
    {
        (require_once __DIR__ . '/Config/Routes.php')($this->app);
    }

    public static function getInstance(): self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function run(): void
    {
        $this->app->run();
    }
}
