<?php

namespace Tests\TestCases\Generators;

use Closure;
use DateTimeInterface;
use Moves\Snowflake\Contracts\ISnowflakeGenerator;
use Moves\Snowflake\Exceptions\SnowflakeBitLengthException;
use Moves\Snowflake\Generators\ModelflakeGenerator;

class ModelflakeGeneratorTest extends TwitterSnowflakeGeneratorTest
{
    const GENERATOR_CLASS = ModelflakeGenerator::class;

    const MODEL_ID = 1;

    protected function getGenerator(
        int $machineId = null,
        Closure $sequenceGenerator = null,
        DateTimeInterface $epoch = null,
        int $modelId = null
    ): ISnowflakeGenerator
    {
        return new (static::GENERATOR_CLASS)(
            $modelId ?? static::MODEL_ID,
            $machineId ?? static::MACHINE_ID,
            $sequenceGenerator ?? function () { return $this->getNextSequenceId(); },
            $epoch
        );
    }


    public function testModelMask()
    {
        $maskBits = decbin((static::GENERATOR_CLASS)::MODEL_MASK);

        $mask1s = substr($maskBits, 0, (static::GENERATOR_CLASS)::BITS_MODEL);
        $count1s = substr_count($mask1s, '1');

        $mask0s = substr($maskBits, (static::GENERATOR_CLASS)::BITS_MODEL);
        $count0s = substr_count($mask0s, '0');

        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_MODEL
            + (static::GENERATOR_CLASS)::BITS_TIMESTAMP
            + (static::GENERATOR_CLASS)::BITS_MACHINE
            + (static::GENERATOR_CLASS)::BITS_SEQUENCE,
            strlen($maskBits)
        );
        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_MODEL,
            $count1s);
        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_TIMESTAMP
            + (static::GENERATOR_CLASS)::BITS_MACHINE
            + (static::GENERATOR_CLASS)::BITS_SEQUENCE,
            $count0s
        );
    }

    public function testModelOverflow()
    {
        $modelId = 1 << (static::GENERATOR_CLASS)::BITS_MODEL;

        $generator = $this->getGenerator(
            null,
            null,
            null,
            $modelId
        );

        $this->expectException(SnowflakeBitLengthException::class);

        $generator->generate();
    }
}
