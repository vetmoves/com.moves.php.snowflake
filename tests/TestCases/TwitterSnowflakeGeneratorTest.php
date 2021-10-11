<?php

namespace Tests\TestCases;

use DateTime;
use Moves\Snowflake\Contracts\ISnowflakeGenerator;
use Moves\Snowflake\Exceptions\SnowflakeBitLengthException;
use Moves\Snowflake\Generators\TwitterSnowflakeGenerator;
use PHPUnit\Framework\TestCase;

class TwitterSnowflakeGeneratorTest extends TestCase
{
    const GENERATOR_CLASS = TwitterSnowflakeGenerator::class;
    
    const MACHINE_ID = 1;

    protected ISnowflakeGenerator $generator;

    static int $sequenceId = -1;

    protected function getNextSequenceId(): int
    {
        static::$sequenceId = (static::$sequenceId + 1) % (1 << (static::GENERATOR_CLASS)::BITS_SEQUENCE);
        return static::$sequenceId;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new (static::GENERATOR_CLASS)(
            static::MACHINE_ID,
            function () { return $this->getNextSequenceId(); }
        );
    }


    public function testTimestampMask()
    {
        $maskBits = decbin((static::GENERATOR_CLASS)::TIMESTAMP_MASK);

        $mask1s = substr($maskBits, 0, (static::GENERATOR_CLASS)::BITS_TIMESTAMP);
        $count1s = substr_count($mask1s, '1');

        $mask0s = substr($maskBits, (static::GENERATOR_CLASS)::BITS_TIMESTAMP);
        $count0s = substr_count($mask0s, '0');

        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_TIMESTAMP
            + (static::GENERATOR_CLASS)::BITS_MACHINE
            + (static::GENERATOR_CLASS)::BITS_SEQUENCE,
            strlen($maskBits)
        );
        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_TIMESTAMP,
            $count1s);
        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_MACHINE + (static::GENERATOR_CLASS)::BITS_SEQUENCE,
            $count0s
        );
    }

    public function testMachineMask()
    {
        $maskBits = decbin((static::GENERATOR_CLASS)::MACHINE_MASK);

        $mask1s = substr($maskBits, 0, (static::GENERATOR_CLASS)::BITS_MACHINE);
        $count1s = substr_count($mask1s, '1');

        $mask0s = substr($maskBits, (static::GENERATOR_CLASS)::BITS_MACHINE);
        $count0s = substr_count($mask0s, '0');

        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_MACHINE
            + (static::GENERATOR_CLASS)::BITS_SEQUENCE,
            strlen($maskBits)
        );
        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_MACHINE,
            $count1s);
        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_SEQUENCE,
            $count0s
        );
    }

    public function testSequenceMask()
    {
        $maskBits = decbin((static::GENERATOR_CLASS)::SEQUENCE_MASK);

        $mask1s = substr($maskBits, 0, (static::GENERATOR_CLASS)::BITS_SEQUENCE);
        $count1s = substr_count($mask1s, '1');

        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_SEQUENCE,
            strlen($maskBits)
        );
        $this->assertEquals(
            (static::GENERATOR_CLASS)::BITS_SEQUENCE,
            $count1s);
    }

//    public function testGeneratesUniqueIds()
//    {
//        $snowflakes = [];
//        $numIds = (2 << (static::GENERATOR_CLASS)::BITS_SEQUENCE);
//
//        for ($i = 0; $i < $numIds; $i++) {
//            $snowflakes[] = $this->generator->generate();
//        }
//
//        $this->assertCount($numIds, array_unique($snowflakes));
//    }

    public function testTimestampOverflow()
    {
        $generator = new (static::GENERATOR_CLASS)(
            static::MACHINE_ID,
            function () { return $this->getNextSequenceId(); },
            new DateTime('1800-01-01')
        );

        $this->expectException(SnowflakeBitLengthException::class);

        $generator->generate();
    }

    public function testMachineOverflow()
    {
        $generator = new (static::GENERATOR_CLASS)(
            1 << (static::GENERATOR_CLASS)::BITS_MACHINE,
            function () { return $this->getNextSequenceId(); }
        );

        $this->expectException(SnowflakeBitLengthException::class);

        $generator->generate();
    }

    public function testSequenceOverflow()
    {
        $generator = new (static::GENERATOR_CLASS)(
            static::MACHINE_ID,
            function () { return (1 << (static::GENERATOR_CLASS)::BITS_SEQUENCE); }
        );

        $this->expectException(SnowflakeBitLengthException::class);

        $generator->generate();
    }

    public function testParse()
    {
        $epochDateTime = ($epoch ?? new DateTime((static::GENERATOR_CLASS)::DEFAULT_EPOCH));
        $epochTimestamp = $epochDateTime->getTimestamp() * (static::GENERATOR_CLASS)::TIMESTAMP_MULTIPLIER;

        $now = intval(microtime(true) * (static::GENERATOR_CLASS)::TIMESTAMP_MULTIPLIER)
            - $epochTimestamp;

        $snowflake = $this->generator->generate();
        $components = $this->generator->parse($snowflake);

        //Compare snowflake timestamp with current timestamp with a 1 ms buffer for compute time
        $this->assertLessThanOrEqual(1, abs($components['timestamp'] - $now));

        $this->assertEquals(static::MACHINE_ID, $components['machine']);
        $this->assertEquals(static::$sequenceId, $components['sequence']);
    }
}