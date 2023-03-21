<?php

namespace App\Domain\Posts;

use App\Application\Application;
use App\Application\ORM\Database;
use App\Application\ORM\RowMapper;
use PDO;

class PostRepository
{

    private RowMapper $rowMapper;

    public function __construct()
    {
        $this->rowMapper = new RowMapper(PostsModel::class);
    }

    /**
     * Permet de récupérer tous les posts depuis la base de données.
     * @return PostsModel[]
     */
    public function fetchAll(): array
    {
        $statement = Database::getInstance()->getPDO()->prepare("SELECT * FROM posts");
        $statement->execute();

        return array_map([$this->rowMapper, 'map'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

}