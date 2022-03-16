<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Moves\Snowflake\Traits\EloquentTwitterSnowflakeId;

class TwitterSnowflakeModel extends Model
{
    use EloquentTwitterSnowflakeId;

    protected $table = 'test_models';

    public $snowflakeFields;

    public $casts = [];
}
