<?php

namespace App\Domain\Users;

enum Roles
{

    case ROLE_ANONYMOUS;
    case ROLE_MODERATOR;
    case ROLE_PUBLISHER;

    public function toString(): string
    {
        return match ($this) {
            self::ROLE_ANONYMOUS => "ROLE_ANONYMOUS",
            self::ROLE_MODERATOR => "ROLE_MODERATOR",
            self::ROLE_PUBLISHER => "ROLE_PUBLISHER",
        };
    }

    public function getPower(): int
    {
        return match ($this) {
            self::ROLE_ANONYMOUS => 0,
            self::ROLE_PUBLISHER => 10,
            self::ROLE_MODERATOR => 20,
        };
    }

    public static function fromName(string $name): self
    {
        return match ($name) {
            "ROLE_MODERATOR" => self::ROLE_MODERATOR,
            "ROLE_PUBLISHER" => self::ROLE_PUBLISHER,
            default => self::ROLE_ANONYMOUS,
        };
    }

    public function isLowerThan(Roles $minimumRole): bool
    {
        return $this->getPower() < $minimumRole->getPower();
    }

}