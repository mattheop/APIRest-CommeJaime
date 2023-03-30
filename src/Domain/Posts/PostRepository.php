<?php

namespace App\Domain\Posts;

use App\Application\ORM\Database;
use App\Application\ORM\RowMapper;
use App\Domain\Users\UserModel;
use PDO;

class PostRepository
{
    public const FETCH_ALL_LIMIT = 15;
    private RowMapper $rowMapper;
    private RowMapper $userMapper;

    private const DEFAULT_FETCH = "SELECT p.*, COUNT(CASE WHEN l.is_up = true THEN 1 ELSE null END) AS likes_count, COUNT(CASE WHEN l.is_up = false THEN 1 ELSE null END) AS dislikes_count FROM posts p JOIN users u ON p.id_user = u.id_user LEFT JOIN liked l ON p.id_post = l.id_post GROUP BY p.id_post";

    public function __construct()
    {
        $this->rowMapper = new RowMapper(PostModel::class);
        $this->userMapper = new RowMapper(UserModel::class);
    }

    /**
     * Permet de récupérer tous les posts depuis la base de données.
     * @return PostModel[]
     */
    public function fetchAll(?int $limit = null, ?int $page = null): array
    {
        $limit ??= self::FETCH_ALL_LIMIT;
        $page ??= 1;

        $offset = ($page - 1) * $limit;

        $statement = Database::getInstance()->getPDO()->prepare(self::DEFAULT_FETCH . " LIMIT ? OFFSET ?");

        $statement->bindParam(1, $limit, PDO::PARAM_INT);
        $statement->bindParam(2, $offset, PDO::PARAM_INT);

        $statement->execute();

        return array_map([$this->rowMapper, 'map'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetch(int $id): ?PostModel
    {
        $statement = Database::getInstance()->getPDO()->prepare(self::DEFAULT_FETCH . ' WHERE id_post = :id');
        $statement->execute(compact('id'));

        if ($statement->rowCount() === 0) {
            return null;
        }

        return $this->rowMapper->map($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function delete(int $id): bool
    {
        $q = Database::getInstance()->getPDO()->prepare("DELETE FROM posts WHERE id_post = ?");
        $q->execute([$id]);

        return $q->rowCount() > 0;
    }

    public function fetchLikesUpUsers(int $idPost): array
    {
        $statement = Database::getInstance()->getPDO()->prepare("select users.* from users, liked where liked.id_post = ? AND liked.id_user = users.id_user AND liked.is_up = 1;");
        $statement->execute([$idPost]);

        return array_map([$this->userMapper, 'map'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchLikesDownUsers(int $idPost): array
    {
        $statement = Database::getInstance()->getPDO()->prepare("select users.* from users, liked where liked.id_post = ? AND liked.id_user = users.id_user AND liked.is_up = 0;");
        $statement->execute([$idPost]);

        return array_map([$this->userMapper, 'map'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

}