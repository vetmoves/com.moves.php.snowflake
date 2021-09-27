<?php

require_once('../Helpers/PHPUnitUtil.php');

use Moves\Snowflake\Generators\SonyflakeGenerator;
use PHPUnit\Framework\TestCase;

class SonyflakeGeneratorTest extends TestCase{

	private ?SonyflakeGenerator $sonyflakeGenerator;
	private string $modelName;

	protected function setup(): void{

		$this->modelName = 'UsersModel';
		$this->sonyflakeGenerator = new SonyflakeGenerator($this->modelName);

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