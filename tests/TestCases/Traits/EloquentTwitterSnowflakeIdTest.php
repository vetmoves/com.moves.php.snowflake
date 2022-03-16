<?php

namespace Tests\TestCases\Traits;

use Illuminate\Support\Facades\Cache;
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

        $this->assertLessThanOrEqual(25, abs($components['timestamp'] - $timestamp));
        $this->assertEquals(config('snowflake.machine_id'), $components['machine']);
        $this->assertEquals(Cache::get('snowflake_sequence'), $components['sequence']);

        return $components;
    }

    public function testSequenceWrapsOnOverflow()
    {
        $sequenceNumbers = [];

        $numIds = (1 << (self::GENERATOR_CLASS)::BITS_SEQUENCE);

        $generator = null;

        // Test generating a number of models equal to the maximum sequence number + 1
        // The first and the last instance should have the same sequence number
        for ($i = 0; $i < $numIds + 1; $i++)
        {
            $model = (self::MODEL_CLASS)::create();

            if (is_null($generator)) {
                $generator = $model->getSnowflakeGenerator();
            }

            $components = $generator->parse($model->id);

            if ($i == $numIds) {
                $this->assertContains($components['sequence'], $sequenceNumbers);
            }

            $sequenceNumbers[] = $components['sequence'];
        }

        $this->assertCount($numIds, array_unique($sequenceNumbers));
    }


    public function testParseSnowflakeId()
    {
        $model = (static::MODEL_CLASS)::create();
        $generator = $model->getSnowflakeGenerator();

        $now = intval(microtime(true) * (static::GENERATOR_CLASS)::TIMESTAMP_MULTIPLIER)
            - $generator->getEpochTimestamp();

        $components = $model->parseSnowflakeId();

        //Compare snowflake timestamp with current timestamp with a 10 ms buffer for compute time
        $this->assertLessThanOrEqual(10, abs($components['timestamp'] - $now));

        $this->assertEquals(config('snowflake.machine_id'), $components['machine']);
        $this->assertEquals(Cache::get('snowflake_sequence'), $components['sequence']);

        return $components;
    }

    public function testParseSnowflakeIdEmpty()
    {
        $class = static::MODEL_CLASS;
        $model = new $class;

        $parsed = $model->parseSnowflakeId();

        $this->assertNull($parsed);
    }
}
