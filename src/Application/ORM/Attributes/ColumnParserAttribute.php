<?php

namespace App\Application\ORM\Attributes;

use App\Application\ORM\ColumnParser\ColumnParser;
use \Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ColumnParserAttribute
{
    private ColumnParser $columnParser;

    /**
     * @param string $parser
     * @throws \Exception
     */
    public function __construct(string $parser)
    {
        $this->parser = new $parser;

        if (!($this->parser instanceof ColumnParser)) {
            throw new \Exception("Parser must implement ColumnParser interface");
        }

    }

    /**
     * @return ColumnParser
     */
    public function getParser(): ColumnParser
    {
        return $this->parser;
    }


}