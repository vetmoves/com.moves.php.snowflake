<?php

namespace Moves\Snowflake\Traits;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;
use Moves\Snowflake\Generators\TwitterSnowflakeGenerator;

trait EloquentTwitterSnowflakeId
{
    use EloquentSnowflakeId;

    protected function getGeneratorClass(): string
    {
        return TwitterSnowflakeGenerator::class;
    }

    public function getSnowflakeGenerator(): ISnowflakeGenerator
    {
        return new TwitterSnowflakeGenerator(
            $this->_getMachineId(),
            $this->_getSequenceGenerator()
        );
    }
}