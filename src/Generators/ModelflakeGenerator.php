<?php

namespace Moves\Snowflake\Generators;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;

/**
 * Class ModelflakeGenerator
 *
 * Custom implementation inspired by Twitter Snowflake.
 * Generate Snowflake IDs with a 7-bit prefix identifier for the model that the ID was generated for
 */
class ModelflakeGenerator implements ISnowflakeGenerator{

	public function __construct(int $prefix){

		// TODO: Implement __construct() method.

	}

	// Returns the server's ID given to it via an ENV variable
	private function getServerIdFromEnv(): int{

		return $_ENV['SERVER_ID'];

	}

	// Generates a unique ID based on server variables
	private function generateServerId(): int{

		return sprintf("%08x", abs(crc32($_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_TIME'] . $_SERVER['REMOTE_PORT'])));

	}

	// Either returns the server ID as identified by an ENV variable or return an ID we generate based on current server variables
	protected function getServerId(): int{

		$serverId = $this->getServerIdFromEnv();
		if(empty($serverEnvId) || !is_int($serverEnvId)){
			$serverId = $this->generateServerId();
		}
		return $serverId;

	}

	// Returns current timestamp in integer format
	protected function getUnixTimestamp(): int{

		return time();

	}

	public function generate(): int{

		// TODO: Implement generate() method.

	}

}