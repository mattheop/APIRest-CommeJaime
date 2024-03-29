<?php

namespace App\Domain\Posts;

use App\Application\Application;
use App\Application\ORM\Attributes\ColumnNameAttribute;
use App\Application\ORM\Attributes\ColumnParserAttribute;
use App\Application\ORM\Attributes\DoNotSaveAttribute;
use App\Application\ORM\ColumnParser\DateTimeColumnParser;
use App\Application\ORM\Model;
use DateTime;
use JsonSerializable;

class PostModel extends Model implements JsonSerializable
{
    protected string $tableName = "posts";
    #[ColumnNameAttribute("id_post")]
    public ?int $id = null;
    public string $title;
    public string $content;
    #[ColumnNameAttribute("created_at")]
    #[ColumnParserAttribute(DateTimeColumnParser::class)]
    public ?DateTime $createdAt = null;
    #[ColumnNameAttribute("id_user")]
    public string $idUser;
    #[ColumnNameAttribute("likes_count")]
    #[DoNotSaveAttribute]
    public ?int $likesCount = null;
    #[ColumnNameAttribute("dislikes_count")]
    #[DoNotSaveAttribute]
    public ?int $dislikesCount = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return PostModel
     */
    public function setTitle(string $title): PostModel
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return PostModel
     */
    public function setContent(string $content): PostModel
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return PostModel
     */
    public function setCreatedAt(DateTime $createdAt): PostModel
    {
        $this->createdAt = $createdAt;
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
     * @param string $idUser
     * @return PostModel
     */
    public function setIdUser(string $idUser): PostModel
    {
        $this->idUser = $idUser;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLikesCount(): ?int
    {
        return $this->likesCount;
    }

    /**
     * @return int|null
     */
    public function getDislikesCount(): ?int
    {
        return $this->dislikesCount;
    }

    public function jsonSerialize(): array
    {
        $json = [
            "type" => "posts",
            "id" => $this->id,
            "attributes" => [
                "title" => $this->title,
                "content" => $this->content,
                "created_at" => $this->createdAt?->format("Y-m-d H:i:s"),
                "id_user" => $this->idUser,
            ],
            "links" => [
                "self" => Application::getInstance()->getApp()->getRouteCollector()->getRouteParser()->urlFor("posts.fetch", ["id" => $this->id]),
            ],
        ];
        return $json;
    }
}