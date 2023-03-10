<?php

namespace App\Application\ORM\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ColumnNameAttribute
{
    private string $dbColumnName;

    public function __construct(string $dbColumnName)
    {
        $this->dbColumnName = $dbColumnName;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->dbColumnName;
    }


}