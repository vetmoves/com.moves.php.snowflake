<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Moves\Snowflake\Traits\EloquentSonyflakeId;

class SonyflakeModel extends Model
{
    use EloquentSonyflakeId;

    protected $table = 'test_models';
}
