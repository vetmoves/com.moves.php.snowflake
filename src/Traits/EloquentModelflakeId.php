<?php

namespace Moves\Snowflake\Traits;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;
use Moves\Snowflake\Generators\ModelflakeGenerator;

trait EloquentModelflakeId
{
    use EloquentSnowflakeId;

    protected function getGeneratorClass(): string
    {
        return ModelflakeGenerator::class;
    }

    public function getSnowflakeGenerator(): ISnowflakeGenerator
    {
        return new ModelflakeGenerator(
            $this->_getModelPrefix(),
            $this->_getMachineId(),
            $this->_getSequenceGenerator()
        );
    }

    protected function _getModelPrefix(): int
    {
        $class = static::class;

        return config("modelflake.$class", -1);
    }
}
