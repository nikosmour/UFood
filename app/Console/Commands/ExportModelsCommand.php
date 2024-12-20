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
     * Define the relative path to the app that is the models
     *
     * @var string
     */
    protected string $modelsPath = 'Models';

    /**
     * Define the relative path to the app that will store the models
     * the interface on the ./Interfaces and the BaseModes on ./Base
     * @var string
     */
    protected string $modelsOutputPath = 'resources/js/models';

    /**
     * Define the path on js that has stored the BaseModel
     * @var string
     */
    protected string $baseModel = '@utilities/BaseModel';


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
        $this->modelsPath = app_path($this->modelsPath);

        $jsOutputPath = base_path($this->modelsOutputPath);

        if ($typeOfClass !== 'base' && !is_dir($jsOutputPath)) {
            mkdir($jsOutputPath, 0755, true);
        }
        if ($typeOfClass !== 'main' && !is_dir($jsOutputPath . '/Base')) {
            mkdir($jsOutputPath . '/Base', 0755, true);
        }
        if ($typeOfClass !== 'main' && !is_dir($jsOutputPath . '/Interfaces')) {
            mkdir($jsOutputPath . '/Interfaces', 0755, true);
        }
        $interfaces = [];

        foreach (scandir($this->modelsPath) as $file) {
            if (str_ends_with($file, '.php')) {
                $className = "App\\Models\\" . pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($className)) {
                    echo PHP_EOL . 'model ' . $className . PHP_EOL;
                    $reflection = new ReflectionClass($className);

                    // Generate JS class and interface
                    if ($typeOfClass != 'main') {
                        $properties = $this->getClassPropertiesWithSchemaAndTypes($reflection);
                        $relationships = $this->getModelRelationships($reflection);
                        $this->generateJsBaseClass($reflection->getShortName() . 'Base', $properties, $relationships, $jsOutputPath . '/Base');
                        $interfaces[] = $this->generateInterface($reflection->getShortName(), $properties, $relationships);
                    }
                    // Generate JS class
                    if ($typeOfClass != 'base')
                        $this->generateJsMainClass($reflection->getShortName(), $jsOutputPath);
                }
            }
        }
        $this->generateInterfaces($interfaces, $jsOutputPath . '/Interfaces');

        $this->info('Models JavaScript classes and interfaces have been generated.');
        return CommandAlias::SUCCESS;
    }


    private function generateInterface(string $modelName, array $properties, array $relationships)
    {
        return <<<TS
export interface I{$modelName}BaseInterface extends Record<string, any> {
{$this->generateInterfaceProperties($properties)}
{$this->generateInterfaceRelationships($relationships)}
}
export interface I{$modelName}BaseData extends Pick<I{$modelName}BaseInterface, keyof I{$modelName}BaseInterface> {
{$this->generateInterfaceProperties($properties)}
{$this->generateInterfaceRelationships($relationships)}
}

TS;

    }

    private function generateInterfaces(array $interfaces, $outputPath)
    {
        $Interfacess = collect($interfaces)
            ->map(fn($interface, $key) => $interface)
            ->implode("\n");
        $InterfacesContent = <<<TS
import * as Enums from '@/plugins/enums';
{$Interfacess}

TS;
        File::put("{$outputPath}/index.d.ts", $InterfacesContent);
        echo PHP_EOL . "{$outputPath}/index.d.ts" . PHP_EOL;

    }

    private function generateInterfaceProperties(array $properties): string
    {
        return collect($properties)
            ->map(function ($type, $prop) {
                if (str_ends_with($type['type'], 'Enum')) {
                    return "    {$prop} : typeof Enums.{$type['type']} | null;";
                }

                return "    {$prop}: {$type['definition']};";
            })
            ->implode("\n\n");
    }

    private function generateInterfaceRelationships(array $relationships): string
    {
        return collect($relationships)
            ->map(function ($rel, $key) {
                return "    {$key}: {$rel['definition']}";
            })
            ->implode("\n");
    }

    /**
     * Generate getters and setters for properties.
     *
     */
    private function generateGettersAndSettersProperties(array $properties): string
    {
        return collect($properties)
            ->map(function ($typeInfo, $prop) {
                $methodName = $prop;
                $type = $typeInfo['type'];
                $definition = $typeInfo['definition'];
                // Handle Enums
                $setter = "value ?? null";
                if (str_contains($type, 'Enum')) {
                    $setter = "this.initToEnum({$type}, value )";
                } // Handle Date/DateTime properties
                elseif (str_contains($type, 'Date')) {
                    $setter = "this.initToDate( value )";
                } // Handle numeric properties
                elseif (str_contains($type, 'number')) {
                    $setter = "this.initToNumber( value )";
                } elseif (str_contains($type, 'boolean')) {
                    $setter = "this.initToBoolean( value )";
                }

                // Handle default case (string,  etc.)

                return <<<JS

    /**
     * Get the value of {$prop}.
     */
    get {$methodName}(): {$definition} {
        return this._{$prop};
    }

    /**
     * Set the value of {$prop}.
     * @param value - The new value.
     */
    set {$methodName}(value : {$definition} ) {
        this._{$prop} = {$setter};
    }
JS;
            })
            ->implode("\n");
    }


    /**
     * Generate getters and setters for relationships.
     *
     */
    private function generateGettersAndSettersRelationships(array $properties): string
    {
        return collect($properties)
            ->map(function ($prop, $relationship) {
                $methodName = $relationship;
                // Handle Enums
                $setter = ($prop['size'] === 'many') ?
                    "this.initRelatedArray( value as any[] | null, {$prop['model']} )" :
                    "this.initRelatedObject( value , {$prop['model']} )";

                // Handle default case (string,  etc.)

                return <<<JS

    /**
     * Get the value of {$relationship}.
     */
    get {$methodName}() : {$prop['definitionModel']} {
        return this._{$relationship};
    }

    /**
     * Set the value of {$relationship}.
     * @param { {$prop['definition']} } value - The new value.
     */
    set {$methodName}(value) {
        this._{$relationship} = {$setter};
    }
JS;
            })
            ->implode("\n");
    }


    /**
     * Generate JavaScript class for a model.
     *
     */
    private function generateJsBaseClass(string $modelName, array $properties, array $relationships, string $outputPath)
    {
        $enums = $this->getEnumImports($properties);
        $enumImports = $this->generateEnumImports($enums, false);
        $relationshipsImports = $this->generateRelationshipImports($relationships);

        $gettersAndSettersProperties = $this->generateGettersAndSettersProperties($properties);
        $gettersAndSettersRelationships = $this->generateGettersAndSettersRelationships($relationships);
        $interfaceData = "I{$modelName}Data";
        $interface = "I{$modelName}Interface";
        $classContent = <<<TS
import BaseModel from '{$this->baseModel}';
import type { {$interface} , {$interfaceData} } from "../Interfaces";
{$enumImports}
{$relationshipsImports}

/**
 * Class representing a $modelName model.
 * @class
 * @extends BaseModel
{$this->generateJsDocProperties($properties)}
{$this->generateJsDocRelationships($relationships)}
 */
export class $modelName extends BaseModel<{$interfaceData},{$interface}> implements {$interface} {
{$this->generateProperty($properties)}
{$this->generatePropertyRelationships($relationships)}

    constructor(data : {$interfaceData} ) {
            super();
            this.initiation(data);
        }

    protected properties() : Array< keyof {$interfaceData}> {
        return [
{$this->generatePropertyInitialization($properties)}
        ];
    }
    
    protected relationships() : Array< keyof {$interfaceData}> {
        return [
{$this->generatePropertyRelationshipsInitialization($relationships)}
        ];
    }
    

{$gettersAndSettersProperties}

{$gettersAndSettersRelationships}
}

export default $modelName;
TS;

        File::put("{$outputPath}/{$modelName}.ts", $classContent);
        echo PHP_EOL . "{$outputPath}/{$modelName}.ts" . PHP_EOL;
    }

    /**
     * Generate JavaScript class for a model.
     *
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
     */
    private function generateProperty(array $properties): string
    {
        return collect($properties)
            ->map(function ($typeInfo, $prop) {
                // Handle Enums
                return
                    <<<TS
            protected _{$prop} : {$typeInfo['definition']} = undefined;
TS;
            })
            ->implode("\n");
    }

    private function generatePropertyRelationships(array $properties): string
    {
        return collect($properties)
            ->map(function ($type, $prop) {
                // Handle Enums
                return
                    <<<TS
            protected _{$prop} : {$type['definitionModel']} = undefined;
TS;
            })
            ->implode("\n");
    }

    /**
     * Generate property initialization code.
     *
     */
    private function generatePropertyInitialization(array $properties): string
    {
        return collect($properties)
            ->map(function ($typeInfo, $prop) {
                // Handle Enums
                return
                    <<<TS
            "{$prop}",
TS;
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
            ->map(fn($prop, $relationship) => <<<TS
            "{$relationship}",
TS
            )
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
            $format = explode(':', $type)[1] ?? 'Y-m-d H:i:s';
//            return "Date - format: $format ";
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
     */
    private function generateJsDocProperties(array $properties): string
    {
        return collect($properties)
            ->map(function ($typeInfo, $prop) {
                $privateProp = "_{$prop}";

                return <<<JS
 * @property {{$typeInfo['definition']}} {$privateProp} (Private property for {$prop})
 * @property {{$typeInfo['definition']}} {$prop} (Getter for {$prop})
JS;
                // * @method void {$prop}(value) (Setter for {$prop})

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
            ->map(fn($rel, $key) => " * @property { $rel[definition] } {$key}")
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
        foreach ($modelInstance->getAppends() as $attribute) {
            $schemaAttributes[$attribute] = 'any';
        }

        foreach ($modelInstance->getHidden() as $hiddenField) {
            unset($schemaAttributes[$hiddenField]);
        }

        return $this->updatePropertiesTypes($schemaAttributes);
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
            ->filter(fn($type) => $this->isEnum('App\\Enum\\' . $type['type']))
            ->map(fn($type) => $type['type'])
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
            return "import { enums } from '@/plugins/enums';";
        }

        return collect($enums)
            ->map(fn($enum) => "import { $enum } from '@enums/$enum';")
            ->unique()
            ->implode("\n");
    }


    /**
     * Generate imports for modelRelationships.
     */
    private function generateRelationshipImports(array $relationships): string
    {
        return collect($relationships)
            ->map(fn($relationship) => "import { $relationship[model] } from '@models/{$relationship['model']}';")
            ->unique()
            ->implode("\n");
    }

    private function getModelRelationships(ReflectionClass $reflection): array
    {
        $relationships = [];
        $modelInstance = $reflection->newInstance();
        $hidden = $modelInstance->getHidden();
        $methods = $reflection->getMethods();
        $modelInstance->created_at = now(); // Set a default value for testing
        $modelInstance->updated_at = now(); // Set a default value for testing
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
                $result = $method->invoke($modelInstance);
                if ($result instanceof Relation && !in_array($method->getName(), $hidden)) {
                    $relationType = (new ReflectionClass($result))->getShortName();
                    $relatedModel = basename(str_replace('\\', '/', get_class($result->getRelated())));
                    // Check if the related model exists in the Models directory
                    $relatedModelPath = $this->modelsPath . '/' . $relatedModel . '.php';

                    if (!file_exists($relatedModelPath)) {
                        // Related model doesn't exist in the Models path
                        $this->error("Related model '{$relatedModel}' not found in Models directory.");
                        continue;
                    }

                    $size = str_ends_with($relationType, 'Many') ? 'many' : 'one';
                    $definition = 'PropertyType<I' . $relatedModel . 'BaseInterface>';
                    $definitionModel = 'PropertyType<' . $relatedModel . '>';

                    $definitionModel = $size === 'one' ? $definitionModel : "PropertyType<Array<" . $definitionModel . '>>';
                    $definition = $size === 'one' ? $definition : "PropertyType<Array<" . $definition . '>>';
                    $relationship = Str::snake($method->getName());
                    $relationships[$relationship] = [
                        'type' => $relationType,
                        'model' => $relatedModel,
                        'size' => $size,
                        'definition' => $definition,
                        'definitionModel' => $definitionModel,
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

    private function updatePropertiesTypes(array $properties): array
    {
        return array_map(function ($type) {
            return [
                'definition' => 'PropertyType<' . $type . '>',
                'type' => $type,
            ];
        }, $properties);
    }


}
