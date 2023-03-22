<?php

namespace App\Domain\Users;

use App\Application\ORM\Attributes\ColumnNameAttribute;
use App\Application\ORM\Attributes\ColumnParserAttribute;
use App\Application\ORM\Model;
use JsonSerializable;

class UserModel extends Model implements JsonSerializable
{
    protected string $tableName = "users";
    #[ColumnNameAttribute("id_user")]
    public ?int $id = null;
    public string $username;
    public string $password;
    #[ColumnParserAttribute(RoleColumnParser::class)]
    public Roles $role = Roles::ROLE_PUBLISHER;

    private string $token;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return UserModel
     */
    public function setId(?int $id): UserModel
    {
        $this->id = $id;
        return $this;
    }



    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return UserModel
     */
    public function setUsername(string $username): UserModel
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return Roles
     */
    public function getRole(): Roles
    {
        return $this->role;
    }

    /**
     * @param Roles $role
     * @return UserModel
     */
    public function setRole(Roles $role): UserModel
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return UserModel
     */
    public function setPassword(string $password): UserModel
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return UserModel
     */
    public function setToken(string $token): UserModel
    {
        $this->token = $token;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "username" => $this->username,
            "role" => $this->role->toString()
        ];
    }
}