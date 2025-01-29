<?php
return [
    'alterableProperties' => [
        "Academic" => [
            'academic_id'
        ],
        "CardApplicant" => [
            "first_year",
        ],
        "Address" => [
            "location",
            "phone",
        ],
        "CardApplicationDocument" => [
            "description",
            "status",
        ],
    ],
    'lastDate' => now()->addDays(30),
    'echoEnabled' => env('ENABLE_ECHO', 'false'),
];