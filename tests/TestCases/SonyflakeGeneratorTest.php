<?php

namespace Tests\TestCases;

use Moves\Snowflake\Generators\SonyflakeGenerator;

class SonyflakeGeneratorTest extends TwitterSnowflakeGeneratorTest
{
    const GENERATOR_CLASS = SonyflakeGenerator::class;

    public function testSequenceMask()
    {
        $maskBits = decbin((static::GENERATOR_CLASS)::SEQUENCE_MASK);

        $mask1s = substr($maskBits, 0, (static::GENERATOR_CLASS)::BITS_SEQUENCE);
        $count1s = substr_count($mask1s, '1');

        $mask0s = substr($maskBits, (static::GENERATOR_CLASS)::BITS_SEQUENCE);
        $count0s = substr_count($mask0s, '0');

        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_SEQUENCE
            + (static::GENERATOR_CLASS)::BITS_MACHINE,
            strlen($maskBits)
        );
        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_SEQUENCE,
            $count1s);
        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_MACHINE,
            $count0s
        );
    }

    public function testMachineMask()
    {
        $maskBits = decbin((static::GENERATOR_CLASS)::MACHINE_MASK);

        $mask1s = substr($maskBits, 0, (static::GENERATOR_CLASS)::BITS_MACHINE);
        $count1s = substr_count($mask1s, '1');

        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_MACHINE,
            strlen($maskBits)
        );
        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_MACHINE,
            $count1s);
    }
}
