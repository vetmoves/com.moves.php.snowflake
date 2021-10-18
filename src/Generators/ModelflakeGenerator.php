<?php

namespace Moves\Snowflake\Generators;

use Closure;
use DateTimeInterface;
use Moves\Snowflake\Exceptions\SnowflakeBitLengthException;

/**
 * Class ModelflakeGenerator
 *
 * Custom implementation inspired by Twitter Snowflake.
 * 63 bits total (64 - 1 sign bit)
 * 8 bits model prefix, 39 bits timestamp (unit 10 ms), 8 bits sequence number, 8 bits machine id
 */
class ModelflakeGenerator extends TwitterSnowflakeGenerator
{
    //region Configuration
    /** @var int Allocated bits for model identifier prefix */
    const BITS_MODEL = 8;

    /** @var int Allocated bits for timestamp */
    const BITS_TIMESTAMP = 39;

    /** @var int Allocated bits for machine id */
    const BITS_SEQUENCE = 8;

    /** @var int Allocated bits for sequence number */
    const BITS_MACHINE = 8;

    /** @var string Default epoch start date */
    const DEFAULT_EPOCH = '2014-09-01 00:00:00 +0000 UTC';

    /** @var int Multiplier from microseconds to timestamp unit 10 ms */
    const TIMESTAMP_MULTIPLIER = 100;
    //endregion

    //region Constants
    public const SEQUENCE_MASK = (PHP_INT_MAX >> (self::BITS_MODEL + self::BITS_TIMESTAMP + self::BITS_MACHINE));

    public const MACHINE_MASK = (PHP_INT_MAX >> (self::BITS_MODEL + self::BITS_TIMESTAMP))
        ^ self::SEQUENCE_MASK;

    public const TIMESTAMP_MASK = (PHP_INT_MAX >> self::BITS_MODEL)
        ^ (self::MACHINE_MASK | self::SEQUENCE_MASK);

    public const MODEL_MASK = PHP_INT_MAX
        ^ (self::TIMESTAMP_MASK | self::MACHINE_MASK | self::SEQUENCE_MASK);
    //endregion

    //region Attributes
    /** @var int Model identifier prefix */
    protected int $model;
    //endregion

    /**
     * ModelflakeGenerator constructor.
     * @param int $model Unique prefix number for model
     * @param int $machineId Unique Machine ID
     * @param Closure $sequenceGenerator Function for generating unique sequence number
     * @param DateTimeInterface|null $epoch Optional epoch start time
     */
    public function __construct(int $model, int $machineId, Closure $sequenceGenerator, DateTimeInterface $epoch = null)
    {
        parent::__construct($machineId, $sequenceGenerator, $epoch);

        $this->model = $model;
    }

    /**
     * @inheritDoc
     * @throws SnowflakeBitLengthException
     */
    public function generate(): int
    {
        return ($this->getModelBits() << (self::BITS_TIMESTAMP + self::BITS_MACHINE + self::BITS_SEQUENCE))
            | ($this->getTimestampBits() << (self::BITS_MACHINE + self::BITS_SEQUENCE))
            | ($this->getMachineBits() << (self::BITS_SEQUENCE))
            | $this->getSequenceBits();
    }

    /**
     * @inheritDoc
     */
    public function parse(int $snowflake): array
    {
        return [
            'model' => $this->parseModelBits($snowflake),
            'timestamp' => $this->parseTimestampBits($snowflake),
            'machine' => $this->parseMachineBits($snowflake),
            'sequence' => $this->parseSequenceBits($snowflake)
        ];
    }

    //region Generate Helpers
    /**
     * @throws SnowflakeBitLengthException
     * @return int The model identifier prefix
     */
    public function getModelBits(): int
    {
        if ($this->model >= (1 << self::BITS_MODEL)) {
            throw new SnowflakeBitLengthException(
                'Model Prefix',
                self::BITS_MODEL,
                $this->model
            );
        }

        return $this->model;
    }
    //endregion

    //region Parse Helpers
    protected function parseModelBits(int $snowflake): int
    {
        return $snowflake & self::MODEL_MASK;
    }
    //endregion
}
