<?php

namespace Moves\Snowflake\Generators;

/**
 * Class SonyflakeGenerator
 *
 * Implementation of Sonyflake, Sony's version of Twitter Snowflake
 * 63 bits total (64 - 1 sign bit)
 * 39 bits timestamp (unit 10 ms), 8 bits sequence number, 16 bits machine id
 *
 * @see https://github.com/sony/sonyflake
 */
class SonyflakeGenerator extends TwitterSnowflakeGenerator
{
    //region Configuration
    /** @var int Allocated bits for timestamp */
    const BITS_TIMESTAMP = 39;

    /** @var int Allocated bits for machine id */
    const BITS_SEQUENCE = 8;

    /** @var int Allocated bits for sequence number */
    const BITS_MACHINE = 16;

    /** @var string Default epoch start date */
    const DEFAULT_EPOCH = '2014-09-01 00:00:00 +0000 UTC';

    /** @var int Multiplier from microseconds to timestamp unit 10 ms */
    const TIMESTAMP_MULTIPLIER = 100;
    //endregion

    //region Constants
    public const MACHINE_MASK = PHP_INT_MAX >> (self::BITS_TIMESTAMP + self::BITS_SEQUENCE);

    public const SEQUENCE_MASK = (PHP_INT_MAX >> self::BITS_TIMESTAMP)
        ^ self::MACHINE_MASK;

    public const TIMESTAMP_MASK = PHP_INT_MAX
        ^ (self::SEQUENCE_MASK | self::MACHINE_MASK);
    //endregion

    /**
     * @inheritDoc
     * @throws \Moves\Snowflake\Exceptions\SnowflakeBitLengthException
     */
    public function generate(): int
    {
        return ($this->getTimestampBits() << (self::BITS_SEQUENCE + self::BITS_MACHINE))
            | ($this->getSequenceBits() << (self::BITS_MACHINE))
            | $this->getMachineBits();
    }

    //region Parse Helpers
    protected function parseSequenceBits(int $snowflake): int
    {
        return ($snowflake & static::SEQUENCE_MASK) >> static::BITS_MACHINE;
    }

    protected function parseMachineBits(int $snowflake): int
    {
        return $snowflake & static::MACHINE_MASK;
    }
    //endregion
}