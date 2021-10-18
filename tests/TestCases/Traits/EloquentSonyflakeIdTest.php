<?php

namespace Tests\TestCases\Traits;

use Moves\Snowflake\Generators\SonyflakeGenerator;
use Tests\Models\SonyflakeModel;

class EloquentSonyflakeIdTest extends EloquentTwitterSnowflakeIdTest
{
    const GENERATOR_CLASS = SonyflakeGenerator::class;

    const MODEL_CLASS = SonyflakeModel::class;
}
