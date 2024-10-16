<?php

namespace Tests\Feature\Http\Controllers;

use App\Enum\UserStatusEnum;
use App\Http\Requests\StoreCardApplicantRequest;
use App\Models\Academic;
use App\Models\Department;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CardApplicantControllerTest extends TestCase
{
//        use RefreshDatabase;
    public function test_store_card_applicant_request_validation()
    {
        $data = [
            'cellphone' => '+306912345678',
            'first_year' => 2023,
            'department_id' => 1,
            'addresses' => [
                [
                    'location' => 'Athens',
                    'phone' => '+302101234567',
                    'is_permanent' => true,
                ],
                [
                    'location' => 'Patras',
                    'phone' => '+302612345678',
                    'is_permanent' => false,
                ],
            ]
        ];

        $request = new StoreCardApplicantRequest();
        $validator = Validator::make($data, $request->rules());
        dump($validator->invalid());

        $this->assertTrue($validator->passes());
    }

    public function test_store_card_applicant_request_fails_validation()
    {
        $data = [
            'cellphone' => 'invalid_number',
            'first_year' => 1999,
            'department_id' => 99999,  // Invalid department
            'addresses' => [
                [
                    'location' => '',
                    'phone' => 'wrong_phone',
                    'is_permanent' => true,
                ],
            ]
        ];

        $request = new StoreCardApplicantRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
    }


    public function test_store_card_applicant_with_valid_data()
    {
        $this->actingAs(Academic::factory()->create(['status' => UserStatusEnum::ERASMUS->value, 'is_active' => true]));

        $department = Department::inRandomOrder(412)->first();
        $data = [
            'cellphone' => '+306912345678',
            'first_year' => 2023,
            'department_id' => $department->id,
            'addresses' => [
                [
                    'location' => 'Athens',
                    'phone' => '+302101234567',
                    'is_permanent' => true,
                ],
                [
                    'location' => 'Patras',
                    'phone' => '+302612345678',
                    'is_permanent' => false,
                ],
            ]
        ];

        $response = $this->postJson('/api/card-applicant', $data);
        $response->assertStatus(201)
            ->assertJson(['message' => 'Card applicant and addresses updated successfully.']);

        $this->assertDatabaseHas('card_applicants', ['cellphone' => '+306912345678']);
        $this->assertDatabaseHas('addresses', ['location' => 'Athens']);
    }

    public function test_store_card_applicant_fails_with_invalid_data()
    {
        $this->actingAs(Academic::factory()->create(['status' => UserStatusEnum::ERASMUS]));

        $data = [
            'cellphone' => 'invalid_number',
            'first_year' => 1999,
            'department_id' => 99999,  // Invalid department
            'addresses' => [
                [
                    'location' => '',
                    'phone' => 'wrong_phone',
                    'is_permanent' => true,
                ],
            ]
        ];

        $response = $this->postJson('/api/card-applicant', $data);

        $response->assertStatus(422);  // Validation error

        // Check validation error messages
        $response->assertJsonValidationErrors(['cellphone', 'first_year', 'department_id', 'addresses.0.location', 'addresses.0.phone']);
    }
}

