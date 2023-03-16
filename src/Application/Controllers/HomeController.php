<?php

namespace App\Application\Controllers;

use App\Application\ORM\Database;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController
{

    public function index(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $response->withStatus(404);
    }

}