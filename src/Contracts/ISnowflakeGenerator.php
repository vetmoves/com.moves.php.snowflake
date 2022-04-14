<?php

namespace Moves\Snowflake\Contracts;

/**
 * Interface ISnowflakeGenerator
 *
 * Interface for Snowflake ID Generators
 */
interface ISnowflakeGenerator
{
    /**
     * Determine if the given integer is a snowflake ID
     * @param int $snowflake
     * @return bool
     */
    public function isSnowflake(int $snowflake): bool;

    /**
     * Parse a Snowflake ID into its component parts
     * @param int $snowflake Previously generated Snowflake ID
     * @return array Parsed Snowflake components
     */
    public function parse(int $snowflake): array;

    /**
     * Generate a unique Snowflake ID
     * @return int 63-bit ID (64 bits -1 sign bit)
     */
    public function generate(): int;
}
