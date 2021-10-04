# Snowflake
## 🚨 WARNING 🚨
This package is only compatible with 64-bit installations of PHP.

## Installation
To add this library into your project, run:
```
composer require moves/snowflake
```

## Usage
Include this trait in your Laravel model by adding the following command to the model class:
```
use Moves\Snowflake\Traits\GeneratesSnowflakeId;
```
Then initialize the generator with the following:
```
_getSnowflakeGenerator(*trait_type*);
```
This will cause a primary key to be added with all model inserts if no primary key has been defined for a specific insert
