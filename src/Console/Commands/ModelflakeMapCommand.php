<?php

namespace Moves\Snowflake\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ModelflakeMapCommand extends Command
{
    protected $signature = 'modelflake:map';

    protected $description = 'Generate Modelflake mapping configuration for model classes to unique prefixes';

    public function handle()
    {
        $models = $this->getModels();

        $this->writeConfigFile($models);
    }

    /**
     * @see https://stackoverflow.com/a/60310985
     * @return Collection
     */
    protected function getModels(): Collection
    {
        $models = collect(File::allFiles(app_path()))
            ->map(function ($item) {
                $path = $item->getRelativePathName();
                $class = sprintf('%s%s',
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\'));

                return $class;
            })
            ->filter(function ($class) {
                $valid = false;

                if (class_exists($class)) {
                    $reflection = new \ReflectionClass($class);
                    $valid = $reflection->isSubclassOf(Model::class) &&
                        !$reflection->isAbstract();
                }

                return $valid;
            });

        return $models->values();
    }

    protected function writeConfigFile(Collection $models) {
        $contents = '<?php' . PHP_EOL . PHP_EOL;
        $contents .= 'return [' . PHP_EOL;

        foreach ($models as $i => $model) {
            $contents .= "\t" . "'$model' => $i," . PHP_EOL;
        }

        $contents .= '];' . PHP_EOL;

        file_put_contents(config_path('modelflake.php'), $contents);

        $this->info('Successfully generated ' . config_path('modelflake.php') . '!');
    }
}
