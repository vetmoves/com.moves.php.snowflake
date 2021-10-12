<?php

namespace Tests\TestCases\Traits;

use DateTime;
use Illuminate\Support\Facades\Cache;
use Moves\Snowflake\Generators\ModelflakeGenerator;
use Moves\Snowflake\Generators\TwitterSnowflakeGenerator;
use Tests\Models\TwitterSnowflakeModel;
use Tests\TestCases\TestCase;

class EloquentTwitterSnowflakeIdTest extends TestCase
{
    const GENERATOR_CLASS = TwitterSnowflakeGenerator::class;

    const MODEL_CLASS = TwitterSnowflakeModel::class;

    public function testGeneratesSnowflakeIdOnCreate()
    {
        $model = (static::MODEL_CLASS)::create();

        $snowflake = $model->id;

        $this->assertNotNull($snowflake);

        $generator = $model->getSnowflakeGenerator();

        $this->assertInstanceOf(static::GENERATOR_CLASS, $generator);

        $timestamp = intval(microtime(true) * (static::GENERATOR_CLASS)::TIMESTAMP_MULTIPLIER)
            - $generator->getEpochTimestamp();

        $components = $generator->parse($snowflake);

        $this->assertLessThanOrEqual(10, abs($components['timestamp'] - $timestamp));
        $this->assertEquals(config('snowflake.machine_id'), $components['machine']);
        $this->assertEquals(Cache::get('snowflake_sequence'), $components['sequence']);

        return $components;
    }
}