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
    //region Constants
    /** @var int Allocated bits for timestamp */
    const BIT_LEN_TIMESTAMP = 39;

    /** @var int Allocated bits for machine id */
    const BIT_LEN_SEQUENCE = 8;

    /** @var int Allocated bits for sequence number */
    const BIT_LEN_MACHINE = 16;

    /** @var string Default epoch start date */
    const DEFAULT_EPOCH = '2014-09-01 00:00:00 +0000 UTC';

    /** @var int Multiplier from microseconds to timestamp unit 10 ms */
    const TIMESTAMP_MULTIPLIER = 100;
    //endregion

    /**
     * @inheritDoc
     */
    public function generate(): int
    {
        return ($this->getTimestampBits() << (self::BIT_LEN_SEQUENCE + self::BIT_LEN_MACHINE))
            | ($this->getSequenceBits() << (self::BIT_LEN_MACHINE))
            | $this->getMachineBits();
    }

    //region Parse Helpers
    protected function parseSequenceBits(int $snowflake): int
    {
        $mask = ((1 << (self::BIT_LEN_SEQUENCE + self::BIT_LEN_MACHINE)) - 1);
        $mask = $mask ^ (1 << self::BIT_LEN_SEQUENCE) - 1;

        return $snowflake & $mask;
    }

    protected function parseMachineBits(int $snowflake): int
    {
        $mask = (1 << self::BIT_LEN_SEQUENCE) - 1;

        return $snowflake & $mask;
    }
    //endregion
}