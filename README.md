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
self::bootGeneratesId(trait_type);
```
The *trait_type* parameter is an optional parameter.  If no value is given, our trait defaults to the custom model generator.  This can be overwritten if *trait_type* is set to **twitter** or **sony**.
<br>
This will cause a primary key to be added with all model inserts if a primary key has not already been defined during a specific insert.