<?php

namespace Moves\Snowflake\Contracts;

/**
 * Interface ISnowflakeGenerator
 *
 * Interface for Snowflake ID Generators
 */
interface ISnowflakeGenerator{

	/**
	 * Generate a unique Snowflake ID
	 *
	 * @return 	int 	63-bit ID (64 bits -1 sign bit)
	 */
	public function generate(): int;

}