<?php

require_once('../Helpers/PHPUnitUtil.php');

use Moves\Snowflake\Generators\ModelflakeGenerator;
use PHPUnit\Framework\TestCase;

class ModelflakeGeneratorTest extends TestCase{

	private ?ModelflakeGenerator $modelflakeGenerator;
	private string $modelName;

	protected function setup(): void{

		$this->modelName = 'UsersModel';
		$this->modelflakeGenerator = new ModelflakeGenerator($this->modelName);

	}

	protected function tearDown(): void{

		$this->modelflakeGenerator = null;

	}

	public function testGenerate(): void{

		$result = $this->modelflakeGenerator->generate();
		$this->assertEquals('integer', gettype($result));
		$this->assertEquals(19, strlen((string)$result));

	}

}