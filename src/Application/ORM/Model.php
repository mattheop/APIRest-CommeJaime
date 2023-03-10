<?php

namespace App\Application\ORM;

use App\Application\ORM\Attributes\ColumnNameAttribute;
use App\Application\ORM\Attributes\ColumnParserAttribute;
use Exception;
use JsonSerializable;
use ReflectionClass;

abstract class Model implements JsonSerializable
{
    protected ?int $id;
    protected string $tableName;

    /**
     * By default, convert CamelCase model properties to snake_case
     * @param $column
     * @return string
     */
    public static function columnRewriter($column): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $column));
    }

    /**
     * @throws Exception
     */
    public function save()
    {
        $db = Database::getInstance()->getPDO();

        $class = new ReflectionClass($this);

        $propsToImplode = [];

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) { // consider only public properties of the providen
            $propertyName = $property->getName();

            // check if property is initialized
            if (!$property->isInitialized($this)) {
                throw new Exception("Property $propertyName is not initialized");
            }

            // skip id property and null values
            if ($propertyName === 'id') continue;
            if ($this->{$propertyName} === null) continue;

            $value = $this->{$propertyName};

            // check if property has ColumnParserAttribute
            // if so, parse the value with the provided parser
            foreach ($property->getAttributes(ColumnParserAttribute::class) as $attribute) {
                $attribute = $attribute->newInstance();
                $value = $attribute->getParser()->parseToDatabase($this->{$propertyName});
            }

            $columnNameAttributes = $property->getAttributes(ColumnNameAttribute::class);

            if (sizeof($columnNameAttributes) > 0) {
                $columnNameAttribute = $columnNameAttributes[0]->newInstance();
                $propertyName = $columnNameAttribute->getColumnName();
            }

            $propsToImplode[] = '`' . self::columnRewriter($propertyName) . '` = ' . $db->quote($value);
        }

        // glue all key value pairs together
        $setClause = implode(',', $propsToImplode);

        if ($this->id !== null) {
            $sqlQuery = 'UPDATE `' . $this->tableName . '` SET ' . $setClause . ' WHERE id = ' . $db->quote($this->id);
        } else {
            $sqlQuery = 'INSERT INTO ' . $this->tableName . ' SET ' . $setClause;
        }

        $result = $db->exec($sqlQuery);

        if (is_bool($result)) {
            throw new Exception($db->errorInfo()[2]);
        }

        if ($this->id === null) {
            $this->id = $db->lastInsertId();
        }

        return $result;
    }

}