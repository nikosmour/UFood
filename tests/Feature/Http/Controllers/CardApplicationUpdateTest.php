<?php

namespace Tests\Feature\Http\Controllers;
use App\Enum\CardStatusEnum;
use App\Models\Academic;
use App\Models\CardApplication;
use App\Models\CardApplicationStaff;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CardApplicationUpdateTest extends TestCase
{
    public function test_get_edit_as_academic()
    {
        // Create a user instance (Academic)
        $user = Academic::inRandomOrder()->has('cardApplicant')->with('cardApplicant.currentCardApplication')->first(); // Assuming 'type' is a column to define user type
        // Create a CardApplication and CardLastUpdate
        $cardApplication = $user->cardApplicant->currentCardApplication;
        // Authenticate the user
        Auth::login(Academic::find($cardApplication->academic_id));

        foreach (CardStatusEnum::enumByName() as $status) {
            $cardApplication->cardLastUpdate()->create(['status' => $status,]);
            // Send GET request to the edit route
            $response = $this->get(route('cardApplication.edit', $cardApplication->id));
            $expectedResponse = ($status->canBeUpdated()) ? ['responseStatus' => 200, 'status' => CardStatusEnum::TEMPORARY_SAVED, 'startErrorMessage' => 'Failed to '] : ['responseStatus' => 403, 'status' => $status, 'startErrorMessage' => 'Failed not to'];
            // Verify the response
            $response->assertStatus($expectedResponse['responseStatus']);
            // Refresh the lastUpdate model to get the updated status
            $lastUpdate = $cardApplication->cardLastUpdate()->first();
            $this->assertEquals($expectedResponse['status'], $lastUpdate->status, $expectedResponse['startErrorMessage'] . ' update the application status  from status ' . $status->name);
        }
    }

    public function test_get_edit_as_card_application_staff()
    {
        // Create a user instance (CardApplicationStaff)
        $user = CardApplicationStaff::inRandomOrder()->first(); // Assuming 'type' is a column to define user type
        // Create a CardApplication and CardLastUpdate
        $cardApplication = CardApplication::inRandomOrder()->first();
        $cardApplication->cardLastUpdate()->create(['status' => CardStatusEnum::SUBMITTED]);

        // Authenticate the user
        Auth::login($user);

        // Send GET request to the edit route
        $response = $this->get(route('cardApplication.edit', $cardApplication->id));
        $response->assertStatus(200);

        // Refresh the lastUpdate model to get the updated status
        $cardLastUpdate = $cardApplication->refresh()->cardLastUpdate;

        // Assert that the status has been updated to 'CHECKING'
        $this->assertEquals(CardStatusEnum::CHECKING, $cardLastUpdate->status, 'Failed start editing by the cardStaff');
        $this->assertEquals($user->id, $cardLastUpdate->card_application_staff_id, 'Failed to define the correct card_application_staff_id');
    }
}
