<?php

namespace Moves\Snowflake\Generators;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;

/**
 * Class TwitterSnowflakeGenerator
 *
 * Implementation of Twitter Snowflake ID Generator
 *
 * @see https://blog.twitter.com/engineering/en_us/a/2010/announcing-snowflake
 * @see https://developer.twitter.com/en/docs/twitter-ids
 */
class TwitterSnowflakeGenerator implements ISnowflakeGenerator{

	private int $microtimeLength;
	private int $addressLength;
	private int $portLength;

	public function __construct(){

		$this->microtimeLength = 41;
		$this->addressLength = 13;
		$this->portLength = 9;

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
		$binRemoteAddress = str_pad(decbin($remoteAddress), $this->addressLength, 0, STR_PAD_LEFT);
		$binRemotePort = str_pad(decbin($remotePort), $this->portLength, 0, STR_PAD_LEFT);

		return (int)bindec($binMicrotime . $binRemoteAddress . $binRemotePort);

	}

}