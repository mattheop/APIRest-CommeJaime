<?php

namespace App\Application\ORM;

use App\Application\ORM\Attributes\ColumnNameAttribute;
use App\Application\ORM\Attributes\ColumnParserAttribute;
use ReflectionClass;
use ReflectionException;

class RowMapper
{
    private $classString;

    public function __construct($classString)
    {
        $this->classString = $classString;
    }


    /**
     * @throws ReflectionException
     */
    public function map($row)
    {
        $class = new ReflectionClass($this->classString);
        $entity = $class->newInstanceWithoutConstructor();
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = Model::columnRewriter($property->getName());
            $columnNameAttributes = $property->getAttributes(ColumnNameAttribute::class);
            if (sizeof($columnNameAttributes) > 0) {
                $columnNameAttribute = $columnNameAttributes[0]->newInstance();
                $propertyName = $columnNameAttribute->getColumnName();
            }

            $value = $row[$propertyName];

            // check if property has ColumnParserAttribute
            // if so, parse the value with the provided parser
            foreach ($property->getAttributes(ColumnParserAttribute::class) as $attribute) {
                $attribute = $attribute->newInstance();
                $value = $attribute->getParser()->parseToObject($value);
            }

            $property->setValue($entity, $value);
        }

        return $entity;
    }

}