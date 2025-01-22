<?php

namespace App\Jobs;

use App\Models\Academic;
use App\Models\Department;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Hash;

class StoreMussaInfoJob implements ShouldQueue
{
    use Queueable;

    public array $output2;
    public array $output;

    public function __construct(array $output2, array $output)
    {
        $this->output2 = $output2;
        $this->output = $output;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->storeMussaInfo($this->output2, $this->output);
    }

    private function storeMussaInfo(array $data, array $moreData)
    {
        // Create a new Academic model instance
        $academic = new Academic();

        // Set connection to 'secondary_mysql'
        $academic->setConnection('secondary_mysql');
        unset($data['department']);
        unset($data['guard']);
        // Populate the model with data
        foreach ($data as $key => $value) {
            $academic->{$key} = $value;
        }
        $academic->password = Hash::make('password');
        // Save the academic record
        $academic->save();

        // Handle the Department creation on 'secondary_mysql'
        $department = Department::on('secondary_mysql')->firstOrCreate(['name' => $moreData['department']]);

        // Prepare additional data for CardApplicant
        $vData = [
            'first_year' => $moreData['first_year'] ?? '1971',
            'department_id' => $department->id,
        ];

        // Use the Academic model to retrieve or create the CardApplicant relationship
        $cardApplicant = $academic->cardApplicant()
//            ->setConnection('secondary_mysql') // Ensure the correct connection is used
            ->updateOrCreate([], $vData);

        // Prepare address data
        $address = [
            'location' => $moreData['home_address'],
            'phone' => $moreData['home_phone'] ?? $moreData['mobile_phone'] ?? $moreData['contact_phone'] ?? $moreData['work_phone'] ?? null,
        ];

        // Update or create the address for the CardApplicant
        $cardApplicant->addresses()
//            ->setConnection('secondary_mysql') // Ensure the correct connection is used
            ->updateOrCreate(['is_permanent' => true], $address);

        // Save the CardApplicant record
        $cardApplicant->save();
    }

}
