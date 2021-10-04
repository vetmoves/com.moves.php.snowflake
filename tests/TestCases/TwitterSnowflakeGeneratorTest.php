<?php

require_once('../Helpers/PHPUnitUtil.php');

use Moves\Snowflake\Generators\TwitterSnowflakeGenerator;
use PHPUnit\Framework\TestCase;

class TwitterSnowflakeGeneratorTest extends TestCase{

	private ?TwitterSnowflakeGenerator $twitterSnowflakeGenerator;

	protected function setup(): void{

		$this->twitterSnowflakeGenerator = new TwitterSnowflakeGenerator();

	}

	protected function tearDown(): void{

		$this->twitterSnowflakeGenerator = null;

	}

	public function testGenerate(): void{

		$result = $this->twitterSnowflakeGenerator->generate();
		$this->assertEquals('integer', gettype($result));
		$this->assertEquals(19, strlen((string)$result));

	}

}