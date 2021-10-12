<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Moves\Snowflake\Traits\EloquentModelflakeId;

class ModelflakeModel extends Model
{
    use EloquentModelflakeId;

    protected $table = 'test_models';

    public static function booting()
    {
        $class = class_basename(self::class);
        config(["snowflake.modelflake.$class" => 1]);
    }
}