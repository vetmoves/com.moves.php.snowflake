<?php

namespace Tests\TestCases;

use Closure;
use DateTime;
use DateTimeInterface;
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

    protected function getGenerator(
        int $machineId = null,
        Closure $sequenceGenerator = null,
        DateTimeInterface $epoch = null
    ): ISnowflakeGenerator
    {
        return new (static::GENERATOR_CLASS)(
            $machineId ?? static::MACHINE_ID,
            $sequenceGenerator ?? function () { return $this->getNextSequenceId(); },
            $epoch
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = $this->getGenerator();
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

    public function testGeneratesUniqueIds()
    {
        $snowflakes = [];
        $numIds = (1 << (static::GENERATOR_CLASS)::BITS_SEQUENCE);

        for ($i = 0; $i < $numIds; $i++) {
            $snowflakes[] = $this->generator->generate();
        }

        $this->assertCount($numIds, array_unique($snowflakes));
    }

    public function testTimestampOverflow()
    {
        $generator = $this->getGenerator(
            null,
            null,
            new DateTime('1800-01-01')
        );

        $this->expectException(SnowflakeBitLengthException::class);

        $generator->generate();
    }

    public function testMachineOverflow()
    {
        $generator = $this->getGenerator(1 << (static::GENERATOR_CLASS)::BITS_MACHINE);

        $this->expectException(SnowflakeBitLengthException::class);

        $generator->generate();
    }

    public function testSequenceOverflow()
    {
        $generator = $this->getGenerator(
            null,
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
