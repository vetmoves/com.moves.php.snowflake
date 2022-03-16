<?php

namespace Tests\TestCases\Traits;

use Illuminate\Support\Facades\Cache;
use Moves\Snowflake\Generators\TwitterSnowflakeGenerator;
use Tests\Models\TwitterSnowflakeModel;
use Tests\TestCases\TestCase;

class EloquentSnowflakeIdTest extends TestCase
{
    const GENERATOR_CLASS = TwitterSnowflakeGenerator::class;

    const MODEL_CLASS = TwitterSnowflakeModel::class;

    public function testGuessSnowflakeFields()
    {
        $model = (static::MODEL_CLASS)::create();

        $model->setAttribute('a_id', 1);
        $model->setAttribute('b_id', 1);
        $model->setAttribute('c_id', 1);

        $snowflakeFields = $model->guessSnowflakeFields();

        $this->assertCount(4, $snowflakeFields);
        $this->assertContains('id', $snowflakeFields);
        $this->assertContains('a_id', $snowflakeFields);
        $this->assertContains('b_id', $snowflakeFields);
        $this->assertContains('c_id', $snowflakeFields);
    }

    public function testGetSnowflakeFields()
    {
        $model = (static::MODEL_CLASS)::create();

        $model->setAttribute('a_id', 1);
        $model->setAttribute('b_id', 1);
        $model->setAttribute('c_id', 1);

        $snowflakeFields = $model->getSnowflakeFields();

        $this->assertCount(4, $snowflakeFields);
        $this->assertContains('id', $snowflakeFields);
        $this->assertContains('a_id', $snowflakeFields);
        $this->assertContains('b_id', $snowflakeFields);
        $this->assertContains('c_id', $snowflakeFields);

        $model->snowflakeFields = ['id'];
        $snowflakeFields = $model->getSnowflakeFields();

        $this->assertCount(1, $snowflakeFields);
        $this->assertContains('id', $snowflakeFields);
    }

    public function testGetCasts()
    {
        $model = (static::MODEL_CLASS)::create();

        $model->casts = [
            'a' => 'int',
            'id' => 'int'
        ];

        $model->setAttribute('a_id', 1);
        $model->setAttribute('b_id', 1);
        $model->setAttribute('c_id', 1);

        $this->assertCount(2, $model->casts);

        $modelCasts = $model->getCasts();
        $this->assertCount(5, $modelCasts);
        $this->assertArrayHasKey('a', $modelCasts);
        $this->assertEquals('int', $modelCasts['a']);
        $this->assertArrayHasKey('id', $modelCasts);
        $this->assertEquals('int', $modelCasts['id']);
        $this->assertArrayHasKey('a_id', $modelCasts);
        $this->assertEquals('string', $modelCasts['a_id']);
        $this->assertArrayHasKey('b_id', $modelCasts);
        $this->assertEquals('string', $modelCasts['b_id']);
        $this->assertArrayHasKey('c_id', $modelCasts);
        $this->assertEquals('string', $modelCasts['c_id']);
    }

    public function testSnowflakeFieldsAutomaticallyCastAsString()
    {
        $model = (static::MODEL_CLASS)::create();

        $data = $model->toArray();

        $this->assertIsString($data['id']);

        $model->snowflakeFields = [];

        $data = $model->toArray();

        $this->assertIsInt($data['id']);
    }
}
