<?php

namespace Moves\Snowflake\Generators;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;

/**
 * Class ModelflakeGenerator
 *
 * Custom implementation inspired by Twitter Snowflake.
 * Generate Snowflake IDs with a 7-bit prefix identifier for the model that the ID was generated for
 */
class ModelflakeGenerator implements ISnowflakeGenerator
{
    public function __construct(int $prefix)
    {
        // TODO: Implement __construct() method.
    }

    public function generate(): int
    {
        // TODO: Implement generate() method.
    }
}