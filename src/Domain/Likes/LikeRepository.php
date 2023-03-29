<?php

namespace App\Domain\Likes;

use App\Application\ORM\Database;
use App\Application\ORM\RowMapper;
use App\Domain\Posts\PostModel;
use PDO;

class LikeRepository
{

    private RowMapper $rowMapper;

    public function __construct()
    {
        $this->rowMapper = new RowMapper(LikeModel::class);
    }

    public function fetchAll(int $idPost): array
    {
        $statement = Database::getInstance()->getPDO()->prepare("SELECT * FROM liked WHERE id_post = ?");
        $statement->execute([$idPost]);

        return array_map([$this->rowMapper, 'map'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchAllByUser(int $idUser): array
    {
        $statement = Database::getInstance()->getPDO()->prepare("SELECT * FROM liked WHERE id_user = ?");
        $statement->execute([$idUser]);

        return array_map([$this->rowMapper, 'map'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @throws \ReflectionException
     */
    public function fetch(int $idLike): ?LikeModel {
        $statement = Database::getInstance()->getPDO()->prepare("SELECT * FROM liked WHERE id_liked = ?");
        $statement->execute([$idLike]);

        if ($statement->rowCount() === 0) {
            return null;
        }

        return $this->rowMapper->map($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function delete(int $idLike): bool
    {
        $q = Database::getInstance()->getPDO()->prepare("DELETE FROM liked WHERE id_liked = ?");
        $q->execute([$idLike]);

        return $q->rowCount() > 0;
    }

    public function deleteAllOfUser(int $idUser): bool
    {
        $q = Database::getInstance()->getPDO()->prepare("DELETE FROM liked WHERE id_user = ?");
        $q->execute([$idUser]);

        return $q->rowCount() > 0;
    }

}