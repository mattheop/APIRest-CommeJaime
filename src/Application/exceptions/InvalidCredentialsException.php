<?php

namespace App\Application\exceptions;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpSpecializedException;

class InvalidCredentialsException extends HttpSpecializedException
{

    protected $code = 401;
    protected $message = "Identifiants invalides";
    protected string $title = "401 Unauthorized";
    protected string $description = "Les identifiants que vous avez fournis ne sont pas valides. Veuillez réessayer.";

}