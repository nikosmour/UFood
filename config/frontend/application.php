<?php
return [
    'alterableProperties' => [
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
];