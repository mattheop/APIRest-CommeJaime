<?php

namespace App\Application\Middleware;

use App\Application\Application;
use App\Application\exceptions\UnauthenticatedException;
use App\Domain\Users\Roles;
use App\Domain\Users\UserModel;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @throws UnauthenticatedException if token is not present in header or is invalid
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Check if token is present in header
        // If not pass to next middleware
        $token = $request->getHeader("Authorization");
        if (empty($token)) {
            return $handler->handle($request);
        }

        $token = $token[0];
        $token = str_replace("Bearer ", "", $token);

        try {
            $decoded = JWT::decode($token, new Key(Application::SECRET, 'HS256'));
        } catch (Exception $e) {
            throw new UnauthenticatedException($request, $e->getMessage());
        }

        // check if role is present in token
        if (!property_exists($decoded, "role") || !property_exists($decoded, "username") || !property_exists($decoded, "id")) {
            throw new UnauthenticatedException($request, "Le token a un format invalide, veuillez
             vous reconnecter pour générer un token valide.");
        }

        $user = new UserModel();
        $user->setUsername($decoded->username);
        $user->setRole(Roles::fromName($decoded->role));
        $user->setToken($token);
        $user->setId($decoded->id);

        return $handler->handle($request->withAttribute("user", $user));
    }
}