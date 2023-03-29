<?php

namespace App\Domain\Likes;

use App\Application\ORM\Attributes\ColumnNameAttribute;
use App\Application\ORM\Model;
use JsonSerializable;

class LikeModel extends Model implements JsonSerializable
{

    protected string $tableName = "liked";
    #[ColumnNameAttribute("id_liked")]
    public ?int $id = null;

    #[ColumnNameAttribute("id_post")]
    public int $idPost;
    #[ColumnNameAttribute("id_user")]
    public int $idUser;
    #[ColumnNameAttribute("is_up")]
    public bool $isUp;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return LikeModel
     */
    public function setId(?int $id): LikeModel
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdPost(): int
    {
        return $this->idPost;
    }

    /**
     * @param int $idPost
     * @return LikeModel
     */
    public function setIdPost(int $idPost): LikeModel
    {
        $this->idPost = $idPost;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdUser(): int
    {
        return $this->idUser;
    }

    /**
     * @param int $idUser
     * @return LikeModel
     */
    public function setIdUser(int $idUser): LikeModel
    {
        $this->idUser = $idUser;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUp(): bool
    {
        return $this->isUp;
    }

    /**
     * @param bool $isUp
     * @return LikeModel
     */
    public function setIsUp(bool $isUp): LikeModel
    {
        $this->isUp = $isUp;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "attributes" => [
                "id_post" => $this->idPost,
                "id_user" => $this->idUser,
                "is_up" => $this->isUp
            ],
        ];
    }
}