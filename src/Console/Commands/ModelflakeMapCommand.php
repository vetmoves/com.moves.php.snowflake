<?php

namespace Moves\Snowflake\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Moves\Snowflake\Traits\EloquentModelflakeId;

class ModelflakeMapCommand extends Command
{
    protected $signature = 'modelflake:map';

    protected $description = 'Generate Modelflake mapping configuration for model classes to unique prefixes.';

    public function handle()
    {
        $configuredModels = $this->getConfiguredModels();
        $this->info($configuredModels->count() . ' model(s) already configured (skipping)');

        $projectModels = $this->getProjectModels();

        $newModels = $projectModels->diff($configuredModels->keys());
        $this->info($newModels->count() . ' new model(s) found');

        $prefix = ($configuredModels->max() ?? -1) + 1;

        $combined = $configuredModels;
        foreach ($newModels as $model) {
            $combined[$model] = $prefix;
            $prefix++;
        }

        if ($this->writeConfigFile($combined) !== false) {
            $this->info('Successfully wrote ' . config_path('modelflake.php'));
        } else {
            $this->warn('There was a problem generating ' . config_path('modelflake.php'));
        }
    }

    /**
     * @see https://stackoverflow.com/a/60310985
     * @return Collection
     */
    protected function getProjectModels(): Collection
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
                        !$reflection->isAbstract() &&
                        in_array(EloquentModelflakeId::class, class_uses($class));
                }

                return $valid;
            });

        return $models->values();
    }

    protected function getConfiguredModels(): Collection
    {
        if (File::exists(config_path('modelflake.php'))) {
            return collect(require config_path('modelflake.php'));
        }

        return collect();
    }

    protected function writeConfigFile(Collection $models) {
        $contents = '<?php' . PHP_EOL . PHP_EOL;
        $contents .= 'return [' . PHP_EOL;

        foreach ($models as $model => $prefix) {
            $contents .= "\t" . "$model::class => $prefix," . PHP_EOL;
        }

        $contents .= '];' . PHP_EOL;

        return file_put_contents(config_path('modelflake.php'), $contents);
    }
}
