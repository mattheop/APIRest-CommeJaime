<?php

namespace App\Application\exceptions;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpSpecializedException;

class UnauthenticatedException extends HttpSpecializedException
{



    protected $code = 401;
    protected $message = "Token JWT invalide ou non présent.";
    protected string $title = "401 Unauthorized";
    protected string $description = "Merci de vous connecter pour accéder à cette ressource. Votre token JWT n'a pas été trouvé ou est invalide.";

    /**
     * @param string $description
     */
    public function __construct(ServerRequestInterface $request, string $description)
    {
        parent::__construct($request);
        $this->description = $description;
    }

}