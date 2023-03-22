<?php

namespace App\Application\exceptions;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpSpecializedException;

class UnauthorizedException extends HttpSpecializedException
{

    protected $code = 401;
    protected $message = "Role insuffisant pour accéder à cette ressource.";
    protected string $title = "401 Unauthorized";
    protected string $description = "Merci de vous connecter avec un compte ayant les droits suffisants pour accéder à cette ressource.";

}