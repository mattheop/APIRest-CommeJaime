<?php

namespace App\Application\Controllers;

use App\Application\Application;
use App\Application\ORM\Database;
use App\Application\services\AuthService;
use App\Domain\Likes\LikeModel;
use App\Domain\Likes\LikeRepository;
use App\Domain\Phrase\PhraseRepository;
use App\Domain\Posts\PostModel;
use App\Domain\Posts\PostRepository;
use App\Domain\Posts\PostRoleBasedJSONSerializer;
use App\Domain\Users\Roles;
use Exception;
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
        $user = AuthService::getUserFromRequest($request, false);

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

        $result = array_reduce($result, function (array $carry, PostModel $item) use ($user) {
            $json = new PostRoleBasedJSONSerializer($item, $user);
            $carry[] = $json->jsonSerialize(false);
            return $carry;
        }, []);

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

        $user = AuthService::getUserFromRequest($request, false);
        $json = new PostRoleBasedJSONSerializer($fetched, $user);

        $response->getBody()->write(json_encode([
            "success" => true,
            "data" => $json
        ]));
        return $response->withStatus(200);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $post = $this->modelRepository->fetch((int)$args["id"]);
        $user = AuthService::getUserFromRequest($request);

        if ($post === null) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "Le post n'existe pas"
            ]));
            return $response->withStatus(404);
        }

        // Si l'utilisateur n'est pas l'auteur du post, on ne peut pas le supprimer
        if ($user->getRole() !== Roles::ROLE_MODERATOR) {
            if ($post->getIdUser() !== $user->getId()) {
                $response->getBody()->write(json_encode([
                    "success" => false,
                    "data" => "Vous n'êtes pas l'auteur de ce post"
                ]));
                return $response->withStatus(403);
            }
        }

        if (!$this->modelRepository->delete((int)$args["id"])) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "Le post n'a pas pu être supprimé"
            ]));
            return $response->withStatus(400);
        }

        $response->getBody()->write(json_encode([
            "success" => true,
        ]));

        return $response->withStatus(200);
    }

    public function patch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $body = $request->getParsedBody();

        $fetched = $this->modelRepository->fetch($args["id"]);

        if ($fetched === null) {
            $response->getBody()->write(json_encode([
                "success" => false,
            ]));
            return $response->withStatus(404);
        }

        $user = AuthService::getUserFromRequest($request);
        if ($user->getRole() !== Roles::ROLE_MODERATOR) {
            if ($fetched->getIdUser() !== $user->getId()) {
                $response->getBody()->write(json_encode([
                    "success" => false,
                    "data" => "Vous n'êtes pas l'auteur de ce post"
                ]));
                return $response->withStatus(403);
            }
        }

        $toUpdate = array_intersect(array_keys($body), ["title", "content"]);
        foreach ($toUpdate as $key) {
            $fetched->{"set" . ucfirst($key)}($body[$key]);
        }

        $fetched->save();

        $response->getBody()->write(json_encode([
            "success" => true,
            "updated_fields" => $toUpdate,
            "data" => (new PostRoleBasedJSONSerializer($fetched, $user))->jsonSerialize()
        ]));

        return $response->withStatus(200);
    }

    /**
     * @throws Exception
     */
    public function post(ServerRequestInterface $request, ResponseInterface $response)
    {
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

        $user = AuthService::getUserFromRequest($request);

        $newPost = new PostModel();
        $newPost->setTitle($body['title']);
        $newPost->setContent($body['content']);
        $newPost->setIdUser($user->getId());
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
        $user = AuthService::getUserFromRequest($request);
        $body = $request->getParsedBody();
        $post = $this->modelRepository->fetch((int)$args["id_post"]);
        if ($post === null) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "Le post n'existe pas"
            ]));
            return $response->withStatus(404);
        }

        $is_up = $body['is_up'] ?? true;

        $likeModel = new LikeModel();
        $likeModel->setIdPost((int)$args["id_post"])
            ->setIdUser($user->getId())
            ->setIsUp((bool)$is_up);

        try {
            $likeModel->save();
        } catch (Exception) {
            $response->getBody()->write(json_encode([
                "success" => false,
                "data" => "Vous avez déjà liké ce post"
            ]));
            return $response->withStatus(400);
        }

        $response->getBody()->write(json_encode([
            "success" => true,
            "data" => $likeModel->jsonSerialize()
        ]));

        return $response->withStatus(200);
    }

    public function deleteLike(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $body = $request->getParsedBody();
        $user = AuthService::getUserFromRequest($request);

        $id_post = (int)$args["id_post"];

        $likeRepository = new LikeRepository();
        $affected = $likeRepository->deleteByPostAndUser($id_post, $user->getId());

        if ($affected == 0) {
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