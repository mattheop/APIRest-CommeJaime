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

        if (is_bool($fetched) && $fetched === false) {
            $response->getBody()->write(json_encode([
                "success" => false,
            ]));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode([
            "success" => true,
            "data" => $fetched
        ]));
        return $response->withStatus(200);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $q = Database::getInstance()->getPDO()->prepare("DELETE FROM posts WHERE id_post = ?");
        $q->execute([(int)$args["id"]]);
        if ($q->rowCount() == 0) {
            $response->getBody()->write(json_encode([
                "success" => false
            ]));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode([
            "success" => true,
        ]));
        return $response->withStatus(200);
    }

    public function post(ServerRequestInterface $request, ResponseInterface $response)
    {
        $request->getParsedBody();
        $body = $request->getParsedBody();

        if (empty($body['title'])) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "Le titre n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }

        if (empty($body['content'])) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "Le contenu n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }

        if (empty($body['id_user'])) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "L'identifiant de l'utilisateur n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }

        $title = $body['title'];
        $content = $body['content'];
        $id_user = $body['id_user'];

        $q = Database::getInstance()->getPDO()->prepare("INSERT INTO posts(title,content,id_user) values(?,?,?)");
        $q->execute([$title, $content, $id_user]);

        $q = Database::getInstance()->getPDO()->prepare("SELECT * FROM posts WHERE id_post = ?");
        $q->execute([Database::getInstance()->getPDO()->lastInsertId()]);
        $fetched = $q->fetch(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode([
            "success" => true,
            "data" => $fetched
        ]));

        return $response->withStatus(200);
    }

    public function postLike(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $request->getParsedBody();
        $body = $request->getParsedBody();

        if (empty($body['id_user'])) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "L'identifiant de l'utilisateur n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }

        $id_user = $body['id_user'];
        $is_up = $body['is_up'] ?? true;
        $id_post = (int)$args["id_post"];

        $q = Database::getInstance()->getPDO()->prepare("INSERT INTO liked(id_post,id_user,is_up) values(?,?,?)");
        $q->execute([$id_post, $id_user, $is_up]);

        if ($q->errorInfo()[0] == 23000) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "Vous avez déjà liké ce post"
            ]));
            return $response->withStatus(400);
        }

        $response->getBody()->write(json_encode([
            "success" => true,
            "data" => compact('id_post', 'id_user', 'is_up')
        ]));

        return $response->withStatus(200);
    }

    public function deleteLike(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $body = $request->getParsedBody();

        if (empty($body['id_user'])) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "L'identifiant de l'utilisateur n'est pas renseigné"
            ]));
            return $response->withStatus(400);
        }

        $id_post = (int)$args["id_post"];
        $id_user = (int)$body['id_user'];

        $q = Database::getInstance()->getPDO()->prepare("DELETE FROM liked WHERE id_post = ? AND id_user = ? ");
        $q->execute([$id_post, $id_user]);
        $q->errorInfo();

        if ($q->rowCount() == 0) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "Vous n'aviez pas liké ce post"
            ]));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode([
            "success" => true,
        ]));

        return $response->withStatus(200);
    }
}