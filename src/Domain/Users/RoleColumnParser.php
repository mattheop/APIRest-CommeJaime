<?php

namespace App\Domain\Users;

use App\Application\ORM\ColumnParser\ColumnParser;

class RoleColumnParser implements ColumnParser
{


    public function parseToDatabase(mixed $value): string
    {
        if($value instanceof Roles) {
            return $value->toString();
        }

        return $value;
    }

    public function parseToObject(mixed $value): Roles
    {
        return Roles::fromName($value);
    }
}