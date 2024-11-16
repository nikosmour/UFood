<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionProperty;


class ExportModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Laravel models to JSON and generate JavaScript classes';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $modelsPath = app_path('Models');
        $jsOutputPath = base_path('resources/js/models');

        if (!is_dir($jsOutputPath)) {
            mkdir($jsOutputPath, 0755, true);
        }

        $models = [];

        foreach (scandir($modelsPath) as $file) {
            if (str_ends_with($file, '.php')) {
                $className = "App\\Models\\" . pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($className)) {
                    $reflection = new ReflectionClass($className);

                    // Collect public properties or fillable fields
                    $fillable = $reflection->getDefaultProperties()['fillable'] ?? [];
                    $properties = count($fillable) > 0 ? $fillable : $this->getClassProperties($reflection);

                    $models[$reflection->getShortName()] = $properties;

                    // Generate JS class
                    $this->generateJsClass($reflection->getShortName(), $properties, $jsOutputPath);
                }
            }
        }

        File::put(base_path('models.json'), json_encode($models, JSON_PRETTY_PRINT));
        $this->info('Models exported to models.json and JavaScript classes generated.');
    }

    /**
     * Get properties of a class through reflection.
     *
     * @param ReflectionClass $reflection
     * @return array
     */
    private function getClassProperties(ReflectionClass $reflection)
    {
        return collect($reflection->getProperties(ReflectionProperty::IS_PUBLIC))
            ->map(fn($prop) => $prop->getName())
            ->values()
            ->toArray();
    }

    /**
     * Generate JavaScript class for a model.
     *
     * @param string $modelName
     * @param array $properties
     * @param string $outputPath
     */
    private function generateJsClass(string $modelName, array $properties, string $outputPath)
    {
        $classContent = <<<JS
import BaseModel from '../utilities/BaseModel';

/**
 * Class representing a $modelName model.
 * 
 * @property {number|null} id
{$this->generateJsDocProperties($properties)}
 */
class $modelName extends BaseModel {
    constructor(data = {}) {
        super(data);
        this.initialize();
    }

    /**
     * Initialize default values.
     */
    initialize() {
{$this->generatePropertyInitialization($properties)}
    }
}

export default $modelName;
JS;
        File::put("{$outputPath}/{$modelName}.js", $classContent);
    }

    /**
     * Generate JSDoc properties for the class.
     *
     * @param array $properties
     * @return string
     */
    private function generateJsDocProperties(array $properties)
    {
        return collect($properties)
            ->map(fn($prop) => " * @property {string|null} $prop")
            ->implode("\n");
    }

    /**
     * Generate property initialization code.
     *
     * @param array $properties
     * @return string
     */
    private function generatePropertyInitialization(array $properties)
    {
        return collect($properties)
            ->map(fn($prop) => "        this.{$prop} = this.{$prop} ?? null;")
            ->implode("\n");
    }
}
