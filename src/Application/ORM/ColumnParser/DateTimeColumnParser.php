<?php

namespace App\Application\ORM\ColumnParser;


use DateTime;
use Exception;

class DateTimeColumnParser implements ColumnParser
{

    public function parseToDatabase(mixed $value): string
    {
        if($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        return $value;
    }

    /**
     * @throws Exception
     */
    public function parseToObject(mixed $value): DateTime
    {
        return new DateTime($value);
    }
}