<?php

namespace Moves\Snowflake\Generators;

/**
 * Class ModelflakeGenerator
 *
 * Custom implementation inspired by Twitter Snowflake.
 * Generate Snowflake IDs with a 7-bit prefix identifier for the model that the ID was generated for
 */
class ModelflakeGenerator{

	public string $modelName;

	public function __construct($model_name){

		$this->modelName = $model_name;

	}

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