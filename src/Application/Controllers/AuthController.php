<?php

namespace App\Application\Controllers;

use App\Application\services\AuthService;
use App\Domain\Users\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

class AuthController
{

    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * @throws ReflectionException
     */
    public function fetch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

        if(empty($body['username']) || empty($body['password'])) {
            return $response->withStatus(400);
        }

        $user = $this->authService->login($body['username'], $body['password'], $request);

        $response->getBody()->write(json_encode([
            "token" => $user->getToken(),
            "details" => $user
        ]));
        return $response->withStatus(200);
    }

}