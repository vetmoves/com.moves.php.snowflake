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
                $generator = $model->getSnowflakeGenerator();

                $keyField = $model->getKeyName();
                $model->$keyField = $generator->generate();
            }
        });
    }

    protected function _getMachineId(): int
    {
        return config('snowflake.machine_id');
    }

    public function _getSequenceGenerator(): Closure
    {
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
}
