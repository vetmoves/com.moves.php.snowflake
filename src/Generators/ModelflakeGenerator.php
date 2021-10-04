<?php

namespace Moves\Snowflake\Generators;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;

/**
 * Class ModelflakeGenerator
 *
 * Custom implementation inspired by Twitter Snowflake.
 * Generate Snowflake IDs with a prefix identifier for the model that the ID was generated for
 */
class ModelflakeGenerator implements ISnowflakeGenerator{

	private string $modelName;
	private int $microtimeLength;
	private int $identifierLength;

	public function __construct($modelName){

		$this->modelName = $modelName;
		$this->microtimeLength = 37;
		$this->identifierLength = 26;

	}

	// Returns current micro timestamp in integer format
	private function getUnixMicroTimestamp(): int{

		return (int)(microtime(true) * 1000);

	}

	// Generates a unique ID
	public function generate(): int{

		$microtime = $this->getUnixMicroTimestamp();
		$remoteAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'localhost';
		$remotePort = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '80';

		$binMicrotime = str_pad(decbin($microtime), $this->microtimeLength, 0, STR_PAD_LEFT);
		$binIdentifier = str_pad(decbin($remoteAddress . $remotePort . $this->modelName), $this->identifierLength, 0, STR_PAD_LEFT);

		return (int)bindec($binMicrotime . $binIdentifier);

	}

}