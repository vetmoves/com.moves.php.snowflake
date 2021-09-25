<?php

namespace Moves\Snowflake\Generators;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;

/**
 * Class SonyflakeGenerator
 *
 * Implementation of Sonyflake, Sony's version of Twitter Snowflake
 *
 * @see https://github.com/sony/sonyflake
 */
class SonyflakeGenerator extends ModelflakeGenerator implements ISnowflakeGenerator{

	// Generates a unique ID based on server variables.  This will generate a unique ID if multiple requests hits the server at the same time
	private function generateServerId(): int{

		return sprintf("%08x", abs(crc32($_SERVER['REMOTE_ADDR'] . $_SERVER['REMOTE_PORT'])));

	}

	public function generate(): int{

		return $this->getUnixTimestamp() . $this->generateCurrentModelId() . $this->generateServerId();

	}

}