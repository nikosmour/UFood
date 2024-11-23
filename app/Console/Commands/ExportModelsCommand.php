<?php

namespace App\Console\Commands;

use BackedEnum;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;


class ExportModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-models // {typeOfClass=base : The category of commands to run (base, main, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Laravel models to JSON and generate JavaScript classes';


    /**
     * Execute the console command.
     */
    public function handle(): bool
    {
        $typeOfClass = $this->argument('typeOfClass');
        $allowedTypes = ['base', 'main', 'all'];

        if (!in_array($typeOfClass, $allowedTypes)) {
            $this->error("Invalid typeOfClass value. Allowed values are: " . implode(', ', $allowedTypes));
            return CommandAlias::FAILURE;
        }
        $modelsPath = app_path('Models');
        $jsOutputPath = base_path('resources/js/models');

        if ($typeOfClass !== 'base' && !is_dir($jsOutputPath)) {
            mkdir($jsOutputPath, 0755, true);
        }
        if ($typeOfClass !== 'main' && !is_dir($jsOutputPath . '/Base')) {
            mkdir($jsOutputPath . '/Base', 0755, true);
        }

//        $models = [];

        foreach (scandir($modelsPath) as $file) {
            if (str_ends_with($file, '.php')) {
                $className = "App\\Models\\" . pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($className)) {
                    echo PHP_EOL . 'model ' . $className . PHP_EOL;
                    $reflection = new ReflectionClass($className);

                    // Generate JS class
                    if ($typeOfClass != 'main') {
                        $properties = $this->getClassPropertiesWithSchemaAndTypes($reflection);
                        $relationships = $this->getModelRelationships($reflection);
                        $this->generateJsBaseClass($reflection->getShortName() . 'Base', $properties, $relationships, $jsOutputPath . '/Base');
                    }
                    // Generate JS class
                    if ($typeOfClass != 'base')
                        $this->generateJsMainClass($reflection->getShortName(), $jsOutputPath);


                }
            }
        }

//        File::put(base_path('models.json'), json_encode($models, JSON_PRETTY_PRINT));
        $this->info('Models JavaScript classes has generated.');
        return CommandAlias::SUCCESS;
    }


    /**
     * Generate getters and setters for properties.
     *
     * @param array $properties
     * @return string
     */
    private function generateGettersAndSetters(array $properties): string
    {
        return collect($properties)
            ->map(function ($type, $prop) {
                $methodName = $prop;
                return <<<JS

    /**
     * Get the value of {$prop}.
     * @returns {{$type}|null}
     */
    get {$methodName}() {
        return this._{$prop};
    }

    /**
     * Set the value of {$prop}.
     * @param {{$type}|null} value - The new value.
     */
    set {$methodName}(value) {
        this._{$prop} = value;
    }
JS;
            })
            ->implode("\n");
    }


    /**
     * Generate JavaScript class for a model.
     *
     * @param string $modelName
     * @param array $properties
     * @param array $relationships
     * @param string $outputPath
     */
    private function generateJsBaseClass(string $modelName, array $properties, array $relationships, string $outputPath)
    {
        $enums = $this->getEnumImports($properties);
        $enumImports = $this->generateEnumImports($enums, false);
        $relationshipsImports = $this->generateRelationshipImports($relationships);

        $gettersAndSetters = $this->generateGettersAndSetters($properties);

        $classContent = <<<JS
import BaseModel from '../../utilities/BaseModel';
{$enumImports}
{$relationshipsImports}

/**
 * Class representing a $modelName model.
 * @class
 * @extends BaseModel
{$this->generateJsDocProperties($properties)}
{$this->generateJsDocRelationships($relationships)}
 */
export class $modelName extends BaseModel {
    constructor(data = {}) {
        super();
        Object.assign(this, this.prepareProperties(data));
    }

    /**
     * Prepare properties based on input data and return an object with these properties.
     * @param {Object} data - The data of the object.
     * @returns {Object} An object containing initialized properties.
     */
    prepareProperties(data) {
        return {
{$this->generatePropertyInitialization($properties)}
{$this->generatePropertyRelationshipsInitialization($relationships)}
        }
    }

{$gettersAndSetters}
}

export default $modelName;
JS;

        File::put("{$outputPath}/{$modelName}.js", $classContent);
        echo PHP_EOL . "{$outputPath}/{$modelName}.js" . PHP_EOL;
    }

    /**
     * Generate JavaScript class for a model.
     *
     * @param string $modelName
     * @param array $properties
     * @param array $relationships
     * @param string $outputPath
     * @param string $parentClass
     * @param string $parentLocation
     */
    private function generateJsMainClass(string $modelName, string $outputPath)
    {
        $parentClass = $modelName . 'Base';
        $classContent = <<<JS
import {$parentClass} from "./Base/{$parentClass}";

/**
 * Class representing a $modelName model.
 * @class
 * @extends {$parentClass}
 */
export class $modelName extends {$parentClass} {
}

export default $modelName;
JS;
        File::put("{$outputPath}/{$modelName}.js", $classContent);
        echo PHP_EOL . "{$outputPath}/{$modelName}.js" . PHP_EOL;
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
            ->map(function ($type, $prop) {
                // Handle Enums
                if (str_ends_with($type, 'Enum')) {
                    return "            {$prop} : this.initToEnum({$type}, data.{$prop}),";
                }

                // Handle Date/DateTime properties
                if ($type === 'Date') {
                    return "            {$prop} : this.initToDate( data.{$prop} ),";
                }

                // Handle numeric properties
                if ($type === 'number') {
                    return "            {$prop} : this.initToNumber( data.{$prop} ),";
                }

                if ($type === 'boolean') {
                    return "            {$prop} : this.initToBoolean( data.{$prop} ),";
                }

                // Handle default case (string,  etc.)
                return "            {$prop} : data.{$prop} ?? null,";
            })
            ->implode("\n");
    }

    /**
     * Generate property initialization code.
     *
     * @param array $relationships
     * @return string
     */
    private function generatePropertyRelationshipsInitialization(array $relationships): string
    {
        return collect($relationships)
            ->map(fn($prop, $relationship) => (str_starts_with($prop['definition'], 'Array')) ?
                "            {$relationship} : this.initRelatedArray( data.{$relationship} , {$prop['model']} )," :
                "            {$relationship} : this.initRelatedObject( data.{$relationship} , {$prop['model']} ),")
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
                $properties[$attribute] = 'Date';
            } else {
                // Default to string if no type information is available
                $properties[$attribute] = 'string';
            }
        }

        // Add timestamps if they exist
        if ($reflection->hasProperty('timestamps') && $modelInstance->timestamps) {
            $properties['created_at'] = 'Date';
            $properties['updated_at'] = 'Date';
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
            return "{$enumName}";
        }
        if (str_starts_with($type, 'date')) {
//            $format = explode(':', $type)[1] ?? 'Y-m-d H:i:s';
//            return "Date /* format: $format */";
            return "Date";

        }
        return match ($type) {
            'int', 'integer',
            'float', 'double', 'decimal' => 'number',
            'string' => 'string',
            'bool', 'boolean' => 'boolean',
            'array', 'json' => 'Array',
            'object' => 'Object',
            'datetime', 'date' => 'Date',
            default => 'any',
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
            ->map(function ($type, $prop) {
                $privateProp = "_{$prop}";

                return <<<JS
 * @property {{$type}|null} {$privateProp} (Private property for {$prop})
 * @property {{$type}|null} {$prop} (Getter for {$prop})
 * @method void {$prop}(value) (Setter for {$prop})
JS;
            })
            ->implode("\n");
    }

    /**
     * Generate JSDoc properties for relationships of a model with accurate types.
     *
     * @param array $relationships
     * @return string
     */
    private function generateJsDocRelationships(array $relationships): string
    {
        return collect($relationships)
            ->map(fn($rel, $key) => " * @property { $rel[definition]|null } {$key}")
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
            'decimal', 'float', 'double', 'mediumint' => 'number',
            'string', 'text', 'char', 'varchar' => 'string',
            'boolean' => 'boolean',
            'date', 'datetime', 'timestamp' => 'Date',
            'year', 'time' => 'string',
            'enum' => 'Object',
            default => 'any',
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
            $schemaAttributes[$dateField] = 'Date';
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
            ->filter(fn($type) => $this->isEnum('App\\Enum\\' . $type))
            ->map(fn($type) => $type)
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
            return "import { enums } from '../../plugins/enums';";
        }

        return collect($enums)
            ->map(fn($enum) => "import { $enum } from '../../enums/$enum';")
            ->unique()
            ->implode("\n");
    }


    /**
     * Generate imports for modelRelationships.
     */
    private function generateRelationshipImports(array $relationships): string
    {
        return collect($relationships)
            ->map(fn($relationship) => "import { $relationship[model] } from '../{$relationship['model']}';")
            ->unique()
            ->implode("\n");
    }

    private function getModelRelationships(ReflectionClass $reflection): array
    {
        $relationships = [];
        $methods = $reflection->getMethods();
        foreach ($methods as $key => $method) {
            // Skip methods that are not public or not defined in the model's class
            if (!$method->isPublic() || $method->class !== $reflection->getName()) {
                continue;
            }

            // Ignore methods with parameters
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }

            try {
                // Call the method and check if it returns a Relation instance
                $modelInstance = $reflection->newInstanceWithoutConstructor();
                $result = $method->invoke($modelInstance);
                if ($result instanceof Relation) {
                    $relationType = (new ReflectionClass($result))->getShortName();
                    $relatedModel = basename(str_replace('\\', '/', get_class($result->getRelated())));
                    $size = str_ends_with($relationType, 'Many') ? 'many' : 'one';
                    $definition = $size === 'one' ? $relatedModel : "Array<$relatedModel>";
                    $relationship = Str::snake($method->getName());
                    $relationships[$relationship] = [
                        'type' => $relationType,
                        'model' => $relatedModel,
                        'size' => $size,
                        'definition' => $definition,
                    ];
                    echo " method: " . $relationship;
                }
            } catch (Throwable $e) {
                // Skip methods that throw errors
                continue;
            }
        }
        return $relationships;
    }


}
