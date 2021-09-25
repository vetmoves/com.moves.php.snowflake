<?php

namespace Moves\Snowflake\Traits;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;
use Moves\Snowflake\Generators\TwitterSnowflakeGenerator;

trait GeneratesSnowflakeId{

	// Takes our current model name and converts it to an integer representation
	private function generateCurrentModelId(): int{

		return sprintf("%08x", abs(crc32(get_class($this))));

	}

	public static function bootGeneratesId(){

		static::creating(function($model){
			if(is_null($model->getKey())){
				$generator = $this->_getSnowflakeGenerator();
				$keyField = $model->getKeyName();
				$model->$keyField = $generator->generate();
			}
		});

	}

	public function _getSnowflakeGenerator(): ISnowflakeGenerator{

		if(method_exists($this, 'getSnowflakeGenerator')){
			return $this->getSnowflakeGenerator();
		}
		return new TwitterSnowflakeGenerator();

	}

}