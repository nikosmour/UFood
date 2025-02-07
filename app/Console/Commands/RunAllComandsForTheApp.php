<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class RunAllComandsForTheApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:command_run;// {category=all : The category of commands to run (basic, extra, all)}';

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
        $category = $this->argument('option');
        $commands = [
            'composer install',
            'php artisan migrate',
            'php artisan db:seed',
            'npm install',
            'php artisan clear-compiled',
            'php artisan ide-helper:generate',
            'php artisan ide-helper:models --write-mixin (-M)',
            'php artisan ide-helper:meta',
            'npm run dev',
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
