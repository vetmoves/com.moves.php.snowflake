<?php

namespace Moves\Snowflake\Generators;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;
use Moves\Snowflake\Generators\ModelflakeGenerator;

/**
 * Class TwitterSnowflakeGenerator
 *
 * Implementation of Twitter Snowflake ID Generator
 *
 * @see https://blog.twitter.com/engineering/en_us/a/2010/announcing-snowflake
 * @see https://developer.twitter.com/en/docs/twitter-ids
 */
class TwitterSnowflakeGenerator extends ModelflakeGenerator implements ISnowflakeGenerator{

	// Generates a unique ID based on server variables and the model we're working with.  This will generate a unique ID if multiple requests hits the server at the same time
	private function generateServerAndModelId(): int{

		return sprintf("%08x", abs(crc32($_SERVER['REMOTE_ADDR'] . $_SERVER['REMOTE_PORT'] . $this->modelName)));

	}

	public function generate(): int{

		return $this->getUnixTimestamp() . $this->generateServerAndModelId();

	}

}