<?php

namespace App\Application\Controllers;

use App\Application\Application;
use App\Application\ORM\Database;
use App\Domain\Phrase\PhraseRepository;
use App\Domain\Posts\PostModel;
use App\Domain\Posts\PostRepository;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostController
{
    private PostRepository $modelRepository;

    public function __construct()
    {
        $this->modelRepository = new PostRepository();
    }

    public function fetchAll(ServerRequestInterface $request, ResponseInterface $response)
    {
        $limit = (int)($request->getQueryParams()["limit"] ?? PostRepository::FETCH_ALL_LIMIT);
        $page = (int)($request->getQueryParams()["page"] ?? 1);
        $result = $this->modelRepository->fetchAll($limit, $page);

        $links = [
            "previous_page" => null,
            "next_page" => null,
        ];
        if ($page > 1) {
            $links["previous_page"] = Application::getInstance()->getRouteParser()->urlFor("posts.fetchAll", [], ["limit" => $limit, "page" => $page - 1]);
        }

        if (count($result) > 0) {
            $links["next_page"] = Application::getInstance()->getRouteParser()->urlFor("posts.fetchAll", [], ["limit" => $limit, "page" => $page + 1]);
        }

        $response->getBody()->write(json_encode([
            "success" => true,
            "parameters" => compact('limit', 'page'),
            "count" => count($result),
            "links" => $links,
            "data" => $result
        ]));
        return $response->withStatus(200);
    }

    public function fetch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $fetched = $this->modelRepository->fetch($args["id"]);

        if ($fetched === null) {
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
        if (!$this->modelRepository->delete((int)$args["id"])) {
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

    /**
     * @throws \Exception
     */
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

        $newPost = new PostModel();
        $newPost->setTitle($body['title']);
        $newPost->setContent($body['content']);
        $newPost->setIdUser($body['id_user']);
        $newPost->save();

        $response->getBody()->write(json_encode([
            "success" => true,
            "data" => $newPost
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