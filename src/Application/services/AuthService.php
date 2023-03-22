<?php

namespace App\Application\services;

use App\Application\Application;
use App\Application\exceptions\InvalidCredentialsException;
use App\Application\exceptions\UnauthenticatedException;
use App\Domain\Users\UserModel;
use App\Domain\Users\UserRepository;
use Firebase\JWT\JWT;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

class AuthService
{


    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * @throws ReflectionException
     */
    public function login(string $username, string $password, RequestInterface $request): UserModel
    {
        $user = $this->userRepository->fetchUserByUsername($username);

        if ($user === null) {
            throw new InvalidCredentialsException($request);
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new InvalidCredentialsException($request);
        }

        $jwtPayload = [
            "id" => $user->getId(),
            "username" => $user->getUsername(),
            "role" => $user->getRole()->toString(),
            "exp" => time() + 3600
        ];

        $user->setToken(JWT::encode($jwtPayload, Application::SECRET, "HS256"));

        return $user;
    }

    public static function getUserFromRequest(ServerRequestInterface $request): UserModel
    {
        $user = $request->getAttribute("user");
        if ($user instanceof UserModel === false) {
            throw new UnauthenticatedException($request, "Vous devez être connecté pour accéder à cette ressource.");
        }

        return $user;
    }
}