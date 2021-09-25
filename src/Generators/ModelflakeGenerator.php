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

	// Returns current timestamp in integer format
	protected function getUnixTimestamp(): int{

		return time();

	}

}