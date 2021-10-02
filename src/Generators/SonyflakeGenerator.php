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
class SonyflakeGenerator implements ISnowflakeGenerator{

	// Generates a unique ID based on server variables and the model we're working with.  This will generate a unique ID if multiple requests hits the server at the same time
	private function generateServerAndModelId(): int{

		$remoteAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'localhost';
		$remotePort = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '80';
		return abs(crc32($remoteAddress . $remotePort . $this->modelName));

	}

	// Returns current timestamp in integer format
	private function getUnixTimestamp(): int{

		return time();

	}

	// Generates a unique ID
	public function generate(): int{

		return (int)((string)$this->getUnixTimestamp() . (string)$this->generateServerAndModelId());

	}

}