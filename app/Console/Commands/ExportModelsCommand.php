<?php

namespace App\Console\Commands;

use BackedEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;


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

                    $properties = $this->getClassPropertiesWithSchemaAndTypes($reflection);

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
     * Generate JavaScript class for a model.
     *
     * @param string $modelName
     * @param array $properties
     * @param string $outputPath
     */
    private function generateJsClass(string $modelName, array $properties, string $outputPath)
    {
        $enums = $this->getEnumImports($properties);
        $enumImports = $this->generateEnumImports($enums, false);

        $classContent = <<<JS
import BaseModel from '../utilities/BaseModel';
{$enumImports}

/**
 * Class representing a $modelName model.
 * 
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
     * Generate property initialization code.
     *
     * @param array $properties
     * @return string
     */
    private function generatePropertyInitialization(array $properties)
    {
        return collect($properties)
            ->map(fn($yf, $prop) => "        this.{$prop} = this.{$prop} ?? null;")
            ->implode("\n");
    }


    /**
     * Export Laravel models to JSON and JavaScript classes with accurate types.
     */
    private function getClassPropertiesWithTypes(ReflectionClass $reflection): array
    {
        $modelInstance = $reflection->newInstance();
        $properties = [];

        // Get fillable attributes
        $fillable = $modelInstance->getFillable();

        // Get cast types
        $casts = $modelInstance->getCasts();

        // Get date attributes
        $dates = $modelInstance->getDates();

        foreach ($fillable as $attribute) {
            if (isset($casts[$attribute])) {
                // Map Laravel casts to JavaScript types
                $properties[$attribute] = $this->mapLaravelTypeToJs($casts[$attribute]);
            } elseif (in_array($attribute, $dates)) {
                $properties[$attribute] = 'Date|null';
            } else {
                // Default to string|null if no type information is available
                $properties[$attribute] = 'string|null';
            }
        }

        // Add timestamps if they exist
        if ($reflection->hasProperty('timestamps') && $modelInstance->timestamps) {
            $properties['created_at'] = 'Date|null';
            $properties['updated_at'] = 'Date|null';
        }

        return $properties;
    }

    /**
     * Map Laravel type casts to JavaScript types.
     */
    private function mapLaravelTypeToJs(string $type): string
    {
        if ($this->isEnum($type)) {
            $enumName = (new ReflectionClass($type))->getShortName();
            // Return the type for enum classes
            return "{$enumName}|null";
        }
        if (str_starts_with($type, 'date')) {
//            $format = explode(':', $type)[1] ?? 'Y-m-d H:i:s';
//            return "Date|null /* format: $format */";
            return "Date|null";

        }
        return match ($type) {
            'int', 'integer',
            'float', 'double', 'decimal' => 'number|null',
            'string' => 'string|null',
            'bool', 'boolean' => 'boolean|null',
            'array', 'json' => 'Array|null',
            'object' => 'Object|null',
            'datetime', 'date' => 'Date|null',
            default => 'any|null',
        };
    }

    /**
     * Generate JSDoc properties for a model with accurate types.
     *
     * @param array $properties
     * @return string
     */
    private function generateJsDocProperties(array $properties): string
    {
        return collect($properties)
            ->map(fn($type, $prop) => " * @property {{$type}} $prop")
            ->implode("\n");
    }

    /**
     * Get all attributes from the database schema for the model.
     */
    private function getSchemaAttributes(string $tableName): array
    {
        $columns = Schema::getColumnListing($tableName);

        return collect($columns)->mapWithKeys(function ($column) use ($tableName) {
            $type = Schema::getColumnType($tableName, $column);
            return [$column => $this->mapDatabaseTypeToJs($type)];
        })->toArray();
    }

    /**
     * Map database column types to JavaScript types.
     */
    private function mapDatabaseTypeToJs(string $type): string
    {
        return match ($type) {
            'integer', 'bigint', 'smallint', 'tinyint', 'int',
            'decimal', 'float', 'double', 'mediumint' => 'number|null',
            'string', 'text', 'char', 'varchar' => 'string|null',
            'boolean' => 'boolean|null',
            'date', 'datetime', 'timestamp' => 'Date|null',
            'year', 'time' => 'string|null',
            'enum' => 'Object|null',
            default => 'any|null',
        };
    }

    private function getClassPropertiesWithSchemaAndTypes(ReflectionClass $reflection): array
    {
        $modelInstance = $reflection->newInstance();
        $tableName = $modelInstance->getTable();

        // Start with schema-derived attributes
        $schemaAttributes = $this->getSchemaAttributes($tableName);

        // Override schema types with Laravel-defined casts or dates
        foreach ($modelInstance->getCasts() as $field => $cast) {
            $schemaAttributes[$field] = $this->mapLaravelTypeToJs($cast);
        }

        foreach ($modelInstance->getDates() as $dateField) {
            $schemaAttributes[$dateField] = 'Date|null';
        }

        foreach ($modelInstance->getHidden() as $hiddenField) {
            unset($schemaAttributes[$hiddenField]);
        }

        return $schemaAttributes;
    }

    /**
     * Detect if a property type is a Laravel enum.
     */
    private function isEnum(string $type): bool
    {
        return class_exists($type) && is_subclass_of($type, BackedEnum::class);
    }

    /**
     * Extract enum names for JavaScript imports.
     */
    private function getEnumImports(array $properties): array
    {
        return collect($properties)
            ->filter(fn($type) => $this->isEnum('App\\Enum\\' . str_replace('|null', '', $type)))
            ->map(fn($type) => str_replace('|null', '', $type))
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Generate imports for enums.
     */
    private function generateEnumImports(array $enums, bool $usePlugin = false): string
    {
        if ($usePlugin) {
            return "import { enums } from '../plugins/enums';";
        }

        return collect($enums)
            ->map(fn($enum) => "import { $enum } from '../enums/$enum';")
            ->implode("\n");
    }
}
