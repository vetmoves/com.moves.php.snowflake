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

	private int $microtimeLength;
	private int $machineLength;
	private int $sequenceLength;

	public function __construct(){

		$this->microtimeLength = 39;
		$this->machineLength = 16;
		$this->sequenceLength = 8;

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
		$binRemoteAddress = str_pad(decbin($remoteAddress), $this->machineLength, 0, STR_PAD_LEFT);
		$binRemotePort = str_pad(decbin($remotePort), $this->sequenceLength, 0, STR_PAD_LEFT);

		return (int)bindec($binMicrotime . $binRemoteAddress . $binRemotePort);

	}

}