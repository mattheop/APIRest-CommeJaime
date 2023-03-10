<?php

namespace App\Application\ORM\ColumnParser;

interface ColumnParser
{
    public function parseToDatabase(mixed $value): String;
    public function parseToObject(mixed $value): mixed;

}