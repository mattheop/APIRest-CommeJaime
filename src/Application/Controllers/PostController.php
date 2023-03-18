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

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $q = Database::getInstance()->getPDO()->prepare("DELETE FROM posts WHERE id_post = ?");
        $q->execute([(int)$args["id"]]);
        if ($q ->rowCount()==0) {
            $response->getBody()->write(json_encode([
                "success" => false
            ]));
            return $response->withStatus(404);
        } else {
            $response->getBody()->write(json_encode([
                "success" => true,
            ]));
        }
        return $response->withStatus(200);
    }

    public function post(ServerRequestInterface $request, ResponseInterface $response)
    {
        $request->getParsedBody();
        $body = $request->getParsedBody();

        $title = $body['title'];
        $content = $body['content'];
        $id_user = $body['id_user'];

        if (!(isset($title))){
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "Le titre n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }
        if (!(isset($content))){
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "Le contenu n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }
        if (!(isset($id_user))){
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "L'identifiant de l'utilisateur n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }
        $q = Database::getInstance()->getPDO()->prepare("INSERT INTO posts(title,content,id_user) values(?,?,?)");
        $q->execute([$title,$content,$id_user]);
        $response->getBody()->write(json_encode([
            "success" => true
        ]));
        return $response->withStatus(200);
    }

    public function postLike(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $request->getParsedBody();
        $body = $request->getParsedBody();

        $id_user = $body['id_user'];
        $is_up = $body['is_up'];
        $id_post = (int)$args["id_post"];

        if (!(isset($id_post))){
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "L'identifiant du post n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }
        if (!(isset($id_user))){
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "L'identifiant de l'utilisateur n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }
        if (!(isset($is_up))){
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "L'etat du like n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }
        $q = Database::getInstance()->getPDO()->prepare("INSERT INTO liked(id_post,id_user,is_up) values(?,?,?)");
        $q->execute([$id_post,$id_user, $is_up]);
        $response->getBody()->write(json_encode([
            "success" => true
        ]));
        return $response->withStatus(200);
    }

}