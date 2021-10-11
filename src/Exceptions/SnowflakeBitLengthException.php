<?php

namespace Moves\Snowflake\Exceptions;

use Exception;

class SnowflakeBitLengthException extends Exception
{
    public function __construct(string $component, int $expected, int $actual)
    {
        $actualBitLen = strlen(decbin($actual));

        parent::__construct(
            "Snowflake $component component expected maximum of $expected bits. "
            . "Received $actual ($actualBitLen bits)."
        );
    }
}