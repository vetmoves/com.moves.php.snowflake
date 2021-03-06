<?php

namespace Tests\TestCases\Traits;

use Moves\Snowflake\Generators\ModelflakeGenerator;
use Tests\Models\ModelflakeModel;

class EloquentModelflakeIdTest extends EloquentTwitterSnowflakeIdTest
{
    const GENERATOR_CLASS = ModelflakeGenerator::class;

    const MODEL_CLASS = ModelflakeModel::class;

    public function testGeneratesSnowflakeIdOnCreate()
    {
        $components = parent::testGeneratesSnowflakeIdOnCreate();

        $class = static::MODEL_CLASS;

        $this->assertEquals(config("modelflake.$class"), $components['model']);

        return $components;
    }

    public function testParseSnowflakeId()
    {
        $components = parent::testParseSnowflakeId();

        $class = static::MODEL_CLASS;

        $this->assertEquals(config("modelflake.$class"), $components['model']);

        return $components;
    }
}
