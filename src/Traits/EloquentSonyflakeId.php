<?php

namespace Moves\Snowflake\Traits;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;
use Moves\Snowflake\Generators\SonyflakeGenerator;

trait EloquentSonyflakeId
{
    use EloquentSnowflakeId;

    protected function getGeneratorClass(): string
    {
        return SonyflakeGenerator::class;
    }

    public function getSnowflakeGenerator(): ISnowflakeGenerator
    {
        return new SonyflakeGenerator(
            $this->_getMachineId(),
            $this->_getSequenceGenerator()
        );
    }
}