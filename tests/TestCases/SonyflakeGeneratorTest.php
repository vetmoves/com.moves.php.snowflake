<?php

require_once('../Helpers/PHPUnitUtil.php');

use Moves\Snowflake\Generators\SonyflakeGenerator;
use PHPUnit\Framework\TestCase;

class SonyflakeGeneratorTest extends TestCase{

	private ?SonyflakeGenerator $sonyflakeGenerator;

	protected function setup(): void{

		$this->sonyflakeGenerator = new SonyflakeGenerator();

	}

	protected function tearDown(): void{

		$this->sonyflakeGenerator = null;

	}

	public function testGenerate(): void{

		$result = $this->sonyflakeGenerator->generate();
		$this->assertEquals('integer', gettype($result));
		$this->assertEquals(19, strlen((string)$result));

	}

}