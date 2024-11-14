<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnumToVueJsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enum-to-vue-js';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate JavaScript enum files for Vue.js from PHP enums';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $enumDirectory = app_path('Enum');
        $outputDirectory = resource_path('js/enums');
        $outputDirectoryOfTotal = resource_path('js/plugins');
        $this->ensureDirectoryExists($outputDirectory);

        $this->info("Searching enum files in directory: $enumDirectory");

        $enumFiles = glob($enumDirectory . '/*.php');
        $this->info("Found " . count($enumFiles) . " enum files.");

        $importStatements = [];
        $registryEntries = [];

        foreach ($enumFiles as $enumFile) {
            $className = basename($enumFile, '.php');
            $enumClass = "App\\Enum\\$className";

            if (class_exists($enumClass)) {
                $this->info("Loading enum class: $enumClass");

                // Assuming `toArray` returns constants as ['KEY' => 'value']
                $constants = $enumClass::toArray();
                $jsConstants = $this->convertToJsEnum($className, $constants);

                // Write each enum to its own file
                $enumFilePath = "$outputDirectory/{$className}.js";
                file_put_contents($enumFilePath, $jsConstants);

                $this->info("Enum saved to: $enumFilePath");

                // Prepare import statements and registry entries
                $importStatements[] = "import { $className } from '../enums/{$className}';";
                $registryEntries[] = "    $className,";
            } else {
                $this->error("Enum class not found: $enumClass");
            }
        }

        // Generate enums.js with all imports and registry setup
        $this->generateEnumsJs($outputDirectoryOfTotal, $importStatements, $registryEntries);

        $this->info("Enum generation complete!");
    }

    /**
     * Ensures the output directory exists.
     */
    protected function ensureDirectoryExists($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Converts PHP enum constants to JavaScript enum class content.
     */
    protected function convertToJsEnum($className, $constants)
    {
        $entries = [];
        foreach ($constants as $key => $value) {
            $key = strtoupper($key); // Ensure uppercase keys
            $entries[] = "    static {$key} = new EnumUnit('{$key}', '{$value}');";
        }
        $entries = implode("\n", $entries);

        return
            "import { BaseEnum } from '../utilities/enums/BaseEnum';
import { EnumUnit } from '../utilities/enums/EnumUnit';

export class {$className} extends BaseEnum {
$entries
}";
    }

    /**
     * Generates enums.js that registers all enums.
     */
    protected function generateEnumsJs($outputDirectory, $importStatements, $registryEntries)
    {
        $imports = implode("\n", $importStatements);
        $registry = implode("\n", $registryEntries);

        $content =
            $imports . "

export const Enums = {
$registry
}

export default Enums;
";

        file_put_contents("$outputDirectory/enums.js", $content);
        $this->info("Generated central enums.js file at $outputDirectory/enums.js");
    }
}


