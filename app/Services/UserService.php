<?php

namespace App\Services;

use App\Enum\UserStatusEnum;
use App\Http\Controllers\Auth\LoginController;
use App\Jobs\StoreMussaInfoJob;
use App\Models\Academic;
use App\Models\CardApplicant;
use App\Models\Department;
use App\Models\User;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use UpatrasUserData\Services\GetUserDataService;

class UserService
{
    /**
     * Attempt to log in the user.
     *
     * @param array $credentials
     * @return array
     */
    public function logIn(array $credentials): array
    {

        if (str_ends_with($credentials['email'], 'example.com'))
            return $this->internalCheck($credentials);
        if (str_contains($credentials['email'], '@'))
            return $this->errorResponse();
        return $this->fromMussa($credentials);

    }

    public function logOut(Request $request)
    {
        return app(LoginController::class)->logout($request);
    }

    public function getApplicantInfo(Academic $user): ?CardApplicant
    {

        /** @var CardApplicant|null $cardApplicant */
        $cardApplicant = CardApplicant::on('secondary_mysql')->whereAcademicId($user->academic_id)
            ->withOnly(['addresses', 'departmentRelation:id,name'])
            ->first();

        if (!$cardApplicant) {
            throw new Exception('Card Applicant not found in secondary database.');
        }

// Convert the fetched CardApplicant to an array

        $data = $cardApplicant->toArray();

// Switch back to the default database

// Handle the Department
        $department = null;
        if (isset($data['department'])) {
            $department = Department::firstOrCreate(
                ['name' => $data['department']]
            );
        }
// Create or update the CardApplicant
        $defaultCardApplicant = $user->cardApplicant()->updateOrCreate(
            [], // Match criteria
            [
                'first_year' => $data['first_year'],
                'department_id' => $department->id ?? null,
            ]
        );

// Handle Addresses
        if (isset($data['addresses']) && is_array($data['addresses'])) {
            foreach ($data['addresses'] as $addressData) {
                $defaultCardApplicant->addresses()->updateOrCreate(
                    [
                        'is_permanent' => $addressData['is_permanent'],
                    ],
                    [
                        'is_permanent' => $addressData['is_permanent'],
                        'location' => $addressData['location'],
                        'phone' => $addressData['phone'],
                    ]
                );
            }
        }

//        new CardApplicant($cardApplicant);
//        $cardApplicant->departmenDepartment::Department::t
        return $cardApplicant->load(['addresses', 'departmentRelation:id,name']);
    }

    public function updateActiveStatusForUsers(): void
    {
        // Fetch active status for all users
        return;
    }

    private function fromMussa(array $credentials)
    {
        $credentials['username'] = $credentials['email'];
        unset($credentials['email']);
        try {
            $response = (new GetUserDataService())($credentials)->toArray();
        } catch (ConnectionException $error) {
            return [
                'error' => 'connection error',
            ];
        }
        if (!$response['success'])
            return $this->errorResponse();
        $output = $response['output'];
        $output2 = [];
        $output2['status'] = match ($output['status']) {
            'Προπτυχιακός Φοιτητής' => UserStatusEnum::UNDERGRADUATE,
            default => UserStatusEnum::PHD,
        };
        if ($output2['status'] === UserStatusEnum::PHD)
            Log::debug('new status', [$output2['status']]);
        $output2['name'] = ($output['first_name'] ?? $output['first_name_latin']) . ' ' .
            ($output['last_name'] ?? $output['last_name_latin']);
        $output2['father_name'] = $output['patronymic'];
        $output2['email'] = $output['email'];
        $output2['academic_id'] = (int)$output['a_m'];
        $output2['a_m'] = (int)$output['a_m'];
        $output2['is_active'] = $output['is_active'];
        StoreMussaInfoJob::dispatch($output2, $output);
        $output2['department'] = $output['department'];
        $output2['guard'] = 'academics';
        return $output2;
    }

    /**
     * @param array $credentials
     * @return array|string[]
     */
    private function internalCheck(array $credentials): array
    {
        $originalConnection = config('database.default');
        DB::setDefaultConnection('secondary_mysql');
        // Loop through all available guards and attempt login
        foreach (config('auth.guards') as $guard => $temp)
            if (!in_array($guard, [
                'sanctum',
                'web'
            ])) {
                /** @var User $user */
                $user = Auth::guard($guard)->getProvider()->retrieveByCredentials($credentials);
                if ($user && Hash::check($credentials['password'], $user->getAuthPassword())) {
                    DB::setDefaultConnection($originalConnection);
                    $array = [
                        'email' => $user->email,
                        'name' => $user->name,
                        'status' => $user->status,
                        'father_name' => $user->father_name,
                        'guard' => $guard,
                    ];
                    if ($user instanceof Academic) {
                        $array['a_m'] = $user->a_m;
                        $array['academic_id'] = $user->academic_id;
                        $array['is_active'] = $user->is_active;
                        $array['department'] = $user->cardApplicant()->withOnly('departmentRelation')->first()?->department;
                    }
                    return $array;
                }
            }
        DB::setDefaultConnection($originalConnection);
        return $this->errorResponse();
    }

    /**
     * @return string[]
     */
    private function errorResponse(): array
    {
        return [
            'error' => 'invalid_credentials',
        ];
    }

}
