<?php

namespace App\Console\Commands;

use App\Enum\CardStatusEnum;
use App\Models\CardApplicationUpdate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateForgottenApplicationStatus extends Command
{
    /**
     *
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-forgotten-application-status {--hours=96 : The number of hours to check for the "checking" status}
    {--json-file=app/commands/update-forgotten-application-status.json : The path in the storage that will retrieve the last time that checked}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command to update forgotten application status that are  on "checking" status for many hours.
    And store the last time that checked';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve the last run time from a file or other persistent storage
        $jsonFilePath = $this->option('json-file');

        // If the file exists, retrieve the last run timestamp
        $lastRunData = ['last_date_check' => '1970-01-01'];
        $jsonFilePath = ($jsonFilePath === '') ? null : storage_path($jsonFilePath);
        if ($jsonFilePath && file_exists($jsonFilePath)) {
            $lastRunData = json_decode(file_get_contents($jsonFilePath), true);
        }
        $lastRunDate = Carbon::parse($lastRunData['last_date_check']);


        // Get the value for hours from the command's option
        $hours = $this->option('hours');
        $newRunDate = now()->subHours($hours);
        // Query models where status is 'checking' and created_at is older than the specified number of hours
        $checking = CardApplicationUpdate::whereBetween('created_at', [$lastRunDate, $newRunDate])
            ->where('status', CardStatusEnum::CHECKING->value)
            ->update(['status' => CardStatusEnum::TEMPORARY_CHECKED->value]);

        // Output info about the successful update
        $this->info("Models updated successfully $checking records where being in 'checking' status for $hours hours. where  updated after $lastRunDate");
        if ($jsonFilePath === null)
            return;
        $lastRunData['last_date_check'] = $newRunDate;
        // Ensure the directory exists before writing to the file
        $directory = dirname($jsonFilePath); // Get the directory path from the full file path

        // If the directory doesn't exist, create it
        if (!is_dir($directory)) {
            mkdir($directory);
        }

        // Save the updated data to the JSON file
        file_put_contents($jsonFilePath, json_encode($lastRunData, JSON_PRETTY_PRINT));
    }

}
