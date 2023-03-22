<?php

namespace App\Domain\Users;

use App\Application\ORM\Database;
use App\Application\ORM\RowMapper;
use App\Domain\Posts\PostModel;
use PDO;

class UserRepository
{

    private RowMapper $rowMapper;

    public function __construct()
    {
        $this->rowMapper = new RowMapper(UserModel::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function fetchUserByUsername(string $username): ?UserModel
    {

        $statement = Database::getInstance()->getPDO()->prepare("SELECT * FROM users where username = :username");
        $statement->bindParam(":username", $username);
        $statement->execute();

        if ($statement->rowCount() === 0) {
            return null;
        }

        $user = $statement->fetch(PDO::FETCH_ASSOC);
        return $this->rowMapper->map($user);
    }

}