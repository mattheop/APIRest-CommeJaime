<?php

namespace App\Domain\Posts;

use App\Application\ORM\Database;
use App\Application\ORM\RowMapper;
use PDO;

class PostRepository
{
    public const FETCH_ALL_LIMIT = 15;
    private RowMapper $rowMapper;

    public function __construct()
    {
        $this->rowMapper = new RowMapper(PostModel::class);
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

        $statement = Database::getInstance()->getPDO()->prepare("SELECT * FROM posts LIMIT ? OFFSET ?");

        $statement->bindParam(1, $limit, PDO::PARAM_INT);
        $statement->bindParam(2, $offset, PDO::PARAM_INT);

        $statement->execute();

        return array_map([$this->rowMapper, 'map'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetch(int $id): ?PostModel
    {
        $statement = Database::getInstance()->getPDO()->prepare('SELECT * FROM posts WHERE id_post = :id');
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

}