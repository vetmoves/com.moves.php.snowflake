<?php

require_once('../Helpers/PHPUnitUtil.php');

use Moves\Snowflake\Generators\ModelflakeGenerator;
use PHPUnit\Framework\TestCase;
use PHPUnit\PHPUnitUtil;

class ModelflakeGeneratorTests extends TestCase{

	private ?ModelflakeGenerator $modelflakeGenerator;
	private string $modelName;

	protected function setup(): void{

		$this->modelName = 'UsersModel';
		$this->modelflakeGenerator = new ModelflakeGenerator($this->modelName);

	}

	protected function tearDown(): void{

		$this->modelflakeGenerator = null;

	}

	public function testClassProperties(): void{

		$this->assertEquals($this->modelflakeGenerator->modelName, $this->modelName);

	}

	public function testGetUnixTimestamp(): void{

		$this->assertGreaterThanOrEqual(1632605117, PHPUnitUtil::callMethod($this->modelflakeGenerator, 'getUnixTimestamp'));

	}

}