<?php

namespace Moves\Snowflake\Generators;

use Closure;
use DateTime;
use DateTimeInterface;
use Moves\Snowflake\Contracts\ISnowflakeGenerator;
use Moves\Snowflake\Exceptions\SnowflakeBitLengthException;

/**
 * Class TwitterSnowflakeGenerator
 *
 * Implementation of Twitter Snowflake ID Generator
 * 63 bits total (64 - 1 sign bit)
 * 41 bits timestamp (unit ms), 10 bits machine id, 12 bits sequence number
 *
 * @see https://blog.twitter.com/engineering/en_us/a/2010/announcing-snowflake
 * @see https://developer.twitter.com/en/docs/twitter-ids
 */
class TwitterSnowflakeGenerator implements ISnowflakeGenerator
{
    //region Constants
    /** @var int Allocated bits for timestamp */
    const BIT_LEN_TIMESTAMP = 41;

    /** @var int Allocated bits for machine id */
    const BIT_LEN_MACHINE = 10;

    /** @var int Allocated bits for sequence number */
    const BIT_LEN_SEQUENCE = 12;

    /** @var string Default epoch start date */
    const DEFAULT_EPOCH = '1970-01-01 00:00:00 +0000 UTC';

    /** @var int Multiplier from microseconds to timestamp unit ms */
    const TIMESTAMP_MULTIPLIER = 1000;
    //endregion

    //region Attributes
    /** @var int Machine ID */
    protected int $machineId;

    /** @var Closure Function to return unique sequence number */
    protected Closure $sequenceGenerator;

    /** @var int Epoch start timestamp */
    protected int $epochTimestamp;
    //endregion

    /**
     * TwitterSnowflakeGenerator constructor.
     * @param int $machineId Unique Machine ID
     * @param Closure $sequenceGenerator Function for generating unique sequence number
     * @param DateTimeInterface|null $epoch Optional epoch start time
     */
    public function __construct(int $machineId, Closure $sequenceGenerator, DateTimeInterface $epoch = null)
    {
        $this->machineId = $machineId;
        $this->sequenceGenerator = $sequenceGenerator;

        $epochDateTime = ($epoch ?? new DateTime(self::DEFAULT_EPOCH));
        $this->epochTimestamp = $epochDateTime->getTimestamp() * self::TIMESTAMP_MULTIPLIER;
    }

    /**
     * @inheritDoc
     */
    public function generate(): int
    {
        return ($this->getTimestampBits() << (self::BIT_LEN_MACHINE + self::BIT_LEN_SEQUENCE))
            | ($this->getMachineBits() << (self::BIT_LEN_SEQUENCE))
            | $this->getSequenceBits();
    }

    /**
     * @inheritDoc
     */
    public function parse(int $snowflake): array
    {
        return [
            'timestamp' => $this->parseTimestampBits($snowflake),
            'machine' => $this->parseMachineBits($snowflake),
            'sequence' => $this->parseSequenceBits($snowflake)
        ];
    }

    //region Generate Helpers
    /**
     * @throws SnowflakeBitLengthException
     * @return int The timestamp
     */
    protected function getTimestampBits(): int
    {
        $timestamp = $this->epochTimestamp - intval(microtime(true) * self::TIMESTAMP_MULTIPLIER);

        if ($timestamp >= (1 << self::BIT_LEN_TIMESTAMP)) {
            throw new SnowflakeBitLengthException(
                'Timestamp',
                self::BIT_LEN_TIMESTAMP,
                $timestamp
            );
        }

        return $timestamp;
    }

    /**
     * @throws SnowflakeBitLengthException
     * @return int The machine ID
     */
    protected function getMachineBits(): int
    {
        if ($this->machineId >= (1 << self::BIT_LEN_MACHINE)) {
            throw new SnowflakeBitLengthException(
                'Machine ID',
                self::BIT_LEN_MACHINE,
                $this->machineId
            );
        }

        return $this->machineId;
    }

    /**
     * @throws SnowflakeBitLengthException
     * @return int The sequence number
     */
    protected function getSequenceBits(): int
    {
        $sequence = ($this->sequenceGenerator)();

        if ($sequence >= (1 << self::BIT_LEN_SEQUENCE)) {
            throw new SnowflakeBitLengthException(
                'Sequence',
                self::BIT_LEN_SEQUENCE,
                $sequence
            );
        }

        return $sequence;
    }
    //endregion

    //region Parse Helpers
    protected function parseTimestampBits(int $snowflake): int
    {
        $mask = PHP_INT_MAX;
        $mask = $mask ^ ((1 << (self::BIT_LEN_MACHINE + self::BIT_LEN_SEQUENCE)) - 1);

        return $snowflake & $mask;
    }

    protected function parseMachineBits(int $snowflake): int
    {
        $mask = (1 << (self::BIT_LEN_MACHINE + self::BIT_LEN_SEQUENCE)) - 1;
        $mask = $mask ^ ((1 << self::BIT_LEN_SEQUENCE) - 1);

        return $snowflake & $mask;
    }

    protected function parseSequenceBits(int $snowflake): int
    {
        $mask = (1 << self::BIT_LEN_SEQUENCE) - 1;

        return $snowflake & $mask;
    }
    //endregion
}