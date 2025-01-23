<?php

namespace Database\Seeders;

use App\Enum\UserStatusEnum;
use App\Models\Academic;
use App\Models\Address;
use App\Models\CardApplicant;
use App\Models\CardApplication;
use App\Models\CardApplicationStaff;
use App\Models\CouponOwner;
use App\Models\CouponStaff;
use App\Models\Department;
use App\Models\EntryStaff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrepareEvaluationMainSeeder extends Seeder
{
    use WithoutModelEvents;

    private bool $extra;

    public function __construct($count = 20, $extra = false)
    {
        $this->extra = $extra;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $query = Department::on('secondary_mysql');
        Department::insertUsing(['*'], $query);

        echo str_pad('copy entry staff', 120, '.', STR_PAD_RIGHT) . PHP_EOL;
        $query = EntryStaff::on('secondary_mysql')->limit(10);
        EntryStaff::insertUsing(['*'], $query);
        echo str_pad('copy coupon staff', 120, '.', STR_PAD_RIGHT) . PHP_EOL;
        $query = CouponStaff::on('secondary_mysql')->limit(10);
        CouponStaff::insertUsing(['*'], $query);
        echo str_pad('copy card staff', 120, '.', STR_PAD_RIGHT) . PHP_EOL;
        $query = CardApplicationStaff::on('secondary_mysql')->limit(10);
        CardApplicationStaff::insertUsing(['*'], $query);
        $now = now();
        $totalAcademics = 100;
        echo str_pad('Academics', 120, '.', STR_PAD_RIGHT) . PHP_EOL;
        $i = 0;
        foreach (UserStatusEnum::cases() as $status) {
            $processedAcademics = 0;
            $percentage = 0;
            $i++;
            foreach (Academic::on('secondary_mysql')->where('status', $status->value)->limit(100)->cursor() as $academic) {
                echo $i . "/8 Copied {$status->value} ({$percentage}% complete)";
                $newAcademic = $academic->replicate();
                $newAcademic->created_at = $academic->created_at;
                $newAcademic->setConnection('mysql');
                $newAcademic->academic_id = $academic->academic_id;
                $newAcademic->save();
                CouponOwner::factory()->for($newAcademic)->create(['created_at' => $newAcademic->created_at]);
                $processedAcademics++;

                // Calculate and display percentage
                $percentage = ($processedAcademics / $totalAcademics) * 100;
                echo "\r";
            }

        }
        $query = CardApplicant::on('secondary_mysql')->whereIn('academic_id', Academic::select('academic_id'));
        CardApplicant::insertUsing(['*'], $query);
        $query = Address::on('secondary_mysql')->whereIn('academic_id', Academic::select('academic_id'));
        Address::insertUsing(['*'], $query);
        echo str_pad('createCardApplications', 120, '.', STR_PAD_RIGHT) . PHP_EOL;
        foreach (CardApplicant::cursor() as $cardApplicant) {
            CardApplication::factory()->for($cardApplicant)->withComment(!in_array($cardApplicant->academic_id % 8, [
                5,
                6
            ]))->withDocs($cardApplicant->academic_id % 2 === 1)->create();
        }
        echo str_pad('fill the rest  of academics', 120, '.', STR_PAD_RIGHT) . PHP_EOL;
        (new AcademicSeeder(0, true, $now))->run();
    }
}
