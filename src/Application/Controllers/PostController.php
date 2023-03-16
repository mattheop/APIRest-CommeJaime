<?php

namespace App\Application\Controllers;

use App\Application\ORM\Database;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostController
{

    public function fetchAll(ServerRequestInterface $request, ResponseInterface $response)
    {
        $q = Database::getInstance()->getPDO()->query("SELECT * FROM posts");
        $result = $q->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode([
            "success" => true, 
            "count" => count($result),
            "data" => $result
        ]));
        return $response->withStatus(200);
    }

    public function fetch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $q = Database::getInstance()->getPDO()->prepare("SELECT * FROM posts WHERE id_post = ?");
        $q->execute([(int)$args["id"]]);
        $fetched = $q->fetch(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode([
            "success" => true,
            "data" => $fetched
        ]));

        if(is_bool($fetched) && $fetched === false){
            return $response->withStatus(404);
        }
        return $response->withStatus(200);
    }

}