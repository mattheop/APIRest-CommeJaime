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
        dump(Database::getInstance()->getPDO()->query("SELECT * FROM posts")->fetchAll(PDO::FETCH_ASSOC));
        dd(Database::getInstance()->getPDO()->errorInfo());
        return $response->withStatus(404);
    }

}