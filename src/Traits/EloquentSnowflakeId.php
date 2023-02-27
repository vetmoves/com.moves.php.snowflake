<?php

namespace Moves\Snowflake\Traits;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

    public function getIncrementing()
    {
        return false;
    }

    public function getCasts()
    {
        $casts = parent::getCasts();

        foreach ($this->getSnowflakeFields() as $field) {
            if (!array_key_exists($field, $casts)) {
                $casts[$field] = 'string';
            }
        }

        return $casts;
    }

    public function getSnowflakeFields(): array
    {
        if (isset($this->snowflakeFields)) {
            return $this->snowflakeFields;
        }

        return $this->guessSnowflakeFields();
    }

    public function guessSnowflakeFields(): array
    {
        $key = $this->getKeyName();
        $keySuffix = Str::of($key)->explode('_')->last();

        return array_filter(
            array_keys($this->attributes),
            function ($field) use ($key, $keySuffix) {
                return str_ends_with($field, "_$keySuffix") || $field == $key;
            }
        );
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
            $lock = Cache::lock('snowflake_sequence_lock');

            $lock->block(3);

            $current = intval(Cache::get('snowflake_sequence_value', -1));
            $new = ($current + 1) % (1 << $generatorClass::BITS_SEQUENCE);

            Cache::put('snowflake_sequence_value', $new);

            optional($lock)->forceRelease();

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
