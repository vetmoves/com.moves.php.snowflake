# Snowflake
## 🚨 WARNINGS 🚨
This package is only compatible with 64-bit installations of PHP.

This package has not been tested against incredibly strenuous loads such as the actual traffic seen
regularly by Twitter.

## Introduction
This is a PHP implementation of Twitter's Snowflake, Sony's Sonyflake, and our own custom
Modelflake ID generator.

Traits are also included which allow you to directly and automatically generate IDs on Eloquent Model classes.

## Installation
To add this library into your project, run:
```
composer require moves/snowflake
```

## Usage
### Eloquent Models
#### Prerequisites
For each separate deployment of your application, be sure to set a unique value for the `SNOWFLAKE_MACHINE_ID`
environment key.

The default implementation of the provided Eloquent Snowflake generator traits requires you to have a configured
Cache driver. 

🚨 **It is important to note that the `file` Cache driver is not supported for deployments on distributed 
infrastructure such as "Serverless"/AWS Lambdas.** 🚨

Snowflake IDs rely on the creation of a unique sequence number. On traditional deployment systems where each
"environment" necessitates exactly one physical or virtual server instance, atomic sequence number generation is 
only necessary between threads on each individual server. However, with modern deployment systems where a single
"environment" may consist of any number of physical or virtual servers, atomic locking must be ensured across
all server instances. This is only possible via a dedicated microservice, or via a shared cache system.

#### Usage
To use the ID generator of your choice on your Eloquent Model classes, simply add the corresponding trait
to your Model.
`Moves\Snowflake\Traits\EloquentTwitterSnowflakeId` for Twitter Snowflake
`Moves\Snowflake\Traits\EloquentSonyflakeId` for Twitter Snowflake
`Moves\Snowflake\Traits\EloquentModelflakeId` for Twitter Snowflake
or
`Moves\Snowflake\Traits\EloquentSnowflakeId` to specify your own custom ID Generator class

```
use Illuminate\Database\Eloquent\Model;
use Moves\Snowflake\Traits\EloquentTwitterSnowflakeId;

public class MyModel extends Model
{
    use EloquentTwitterSnowflakeId;
}
```

On `create`, your model class will automatically generate a unique ID and apply it to your model instance before
it is inserted into the database.

#### Overriding Default Sequence Number Generation
The supplied traits provide a default implementation for generating a unique sequence number, relying on Laravel's
Cache facade and atomic locking. However, you can override the default implementation by providing your own
function closure which returns a unique sequence number. 
See [Providing Your Own Generator](#Providing-Your-Own-Generator) for more details.

#### Providing Your Own Generator
To provide your own generator class, use the `Moves\Snowflake\Traits\EloquentSnowflakeId` trait, and implement the
following method header:
```
public function getSnowflakeGenerator(): ISnowflakeGenerator
{
    //TODO: Implement
}
```

You can build your own class which implements the `ISnowflakeGenerator` interface, or you can provide an instance
of one of the generators from this package with alternate values passed to the constructor.

For example, this is how you might provide your own sequence number generator function to the TwitterSnowflake
generator:
```
public function getSnowflakeGenerator(): ISnowflakeGenerator
{
    return new TwitterSnowflakeGenerator(
        $this->_getMachineId(),
        function (): int {
            // Closure containing your custom sequence number generation logic
        }
    );
}
```

### Direct Usage
This package is still usable outside of Eloquent model classes. Instantiate and call the appropriate generator
class wherever you need it:

```
$generator = new TwitterSnowflakeGenerator(
    $machineId,
    $sequenceNumberClosure
);

$id = $generator->generate;
```

You can also use the generators to parse an existing Snowflake ID into its component parts:
```
$parts = $generator->parse($snowflake);
// Expected Output:
// [timestamp] => 1634677016824 (ms)
// [machine] => 1
// [sequence] => 1
```
