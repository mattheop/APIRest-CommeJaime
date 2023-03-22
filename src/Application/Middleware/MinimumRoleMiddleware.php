<?php

namespace App\Application\Middleware;

use App\Application\exceptions\UnauthenticatedException;
use App\Application\services\AuthService;
use App\Domain\Users\Roles;
use App\Domain\Users\UserModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MinimumRoleMiddleware implements MiddlewareInterface
{

    private Roles $minimumRole;

    public function __construct(Roles $minimumRole)
    {
        $this->minimumRole = $minimumRole;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = AuthService::getUserFromRequest($request);

        if ($user->getRole()->isLowerThan($this->minimumRole)) {
            throw new UnauthenticatedException($request, "Vous n'avez pas les droits pour accéder à cette ressource.");
        }

        return $handler->handle($request);
    }
}