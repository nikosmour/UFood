<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class PrepareForEvaluation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prepare-evaluation-db;// {category=all : The category of commands to run (basic, extra, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all the command to have a functional App';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        $category = $this->argument('option');
        $commands = [
            'php artisan migrate --database=third_mysql --path=database/migrations/extra  --no-interaction',
            'php artisan migrate --database=mysql --path=database/migrations/common/*  --no-interaction',
            'php artisan migrate --database=mysql --path=database/migrations/main/*  --no-interaction',
            'php artisan migrate --database=secondary_mysql --path=database/migrations/common/*  --seed  --no-interaction',
            'php artisan db:seed PrepareEvaluationMainSeeder  --database=mysql  --no-interaction',
            // Add more commands here as needed
        ];
        // Run each command
        foreach ($commands as $command) {
            $this->executeCommandWithInteractiveInput($command);
        }

    }

    private function executeCommandWithInteractiveInput($command): void
    {
        $descriptorSpec = [
            0 => STDIN,
            1 => STDOUT,
            2 => STDERR,
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);

        if (is_resource($process)) {
            proc_close($process);
        }
    }
}
