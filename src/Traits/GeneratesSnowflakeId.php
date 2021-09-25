<?php

namespace Moves\Snowflake\Traits;

use Moves\Snowflake\Contracts\ISnowflakeGenerator;
use Moves\Snowflake\Generators\SonyflakeGenerator;
use Moves\Snowflake\Generators\TwitterSnowflakeGenerator;

trait GeneratesSnowflakeId{

	public static function bootGeneratesId($type = 'twitter'){

		static::creating(function($model)use($type){
			if(is_null($model->getKey())){
				$generator = $this->_getSnowflakeGenerator($type);
				$keyField = $model->getKeyName();
				$model->$keyField = $generator->generate();
			}
		});

	}

	// Returns the appropriate generator
	public function _getSnowflakeGenerator($type): ISnowflakeGenerator{

		if(method_exists($this, 'getSnowflakeGenerator')){
			return $this->getSnowflakeGenerator();
		}
		if($type === 'sony'){
			return new SonyflakeGenerator(get_class($this));
		}
		else{
			return new TwitterSnowflakeGenerator(get_class($this));
		}

	}

}