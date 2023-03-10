<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class TrailingSlashMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path != '/' && substr($path, -1) == '/') {
            // recursively remove slashes when its more than 1 slash
            $path = rtrim($path, '/');

            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath($path);

            if ($request->getMethod() == 'GET') {
                $response = new Response();
                return $response
                    ->withHeader('Location', (string) $uri)
                    ->withStatus(301);
            } else {
                $request = $request->withUri($uri);
            }
        }

        return $handler->handle($request);
    }
}