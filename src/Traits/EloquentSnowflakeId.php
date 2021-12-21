<?php

namespace Moves\Snowflake\Traits;

use Closure;
use Illuminate\Support\Facades\Cache;

trait EloquentSnowflakeId
{
    public static function bootEloquentSnowflakeId()
    {
        static::creating(function ($model) {
            if (is_null($model->getKey())) {
                $model->generateSnowflakeId();
            }
        });
    }

    public function generateSnowflakeId()
    {
        $generator = $this->getSnowflakeGenerator();

        $keyField = $this->getKeyName();
        $this->$keyField = $generator->generate();
    }

    protected function _getMachineId(): int
    {
        if (method_exists($this, 'getMachineId')) {
            return $this->getMachineId();
        }

        return config('snowflake.machine_id');
    }

    public function _getSequenceGenerator(): Closure
    {
        if (method_exists($this, 'getSequenceGenerator')) {
            return $this->getSequenceGenerator();
        }

        $generatorClass = $this->getGeneratorClass();

        return function() use ($generatorClass): int  {
            $lock = Cache::lock('snowflake_sequence');

            $lock->block(3);

            $current = Cache::get('snowflake_sequence', -1);
            $new = ($current + 1) % (1 << $generatorClass::BITS_SEQUENCE);

            Cache::put('snowflake_sequence', $new);

            optional($lock)->release();

            return $new;
        };
    }

    public function parseSnowflakeId(): ?array
    {
        if (!is_null($this->getKey())) {
            return $this->getSnowflakeGenerator()->parse($this->getKey());
        }

        return null;
    }
}
