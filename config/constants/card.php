<?php
return [
    'application' => [

        /*
       |--------------------------------------------------------------------------
       | Card Application Status
       |--------------------------------------------------------------------------
       |
       | Here is written all the status that can be a card application. That is used
       | in the migration of the card applications table.So if you have already made
       | the migration it is important to change also the values in the database.
       |
       */

        'status' => \App\Enum\CardStatusEnum::values(),

        /*
       |--------------------------------------------------------------------------
       | Card Application Document Status
       |--------------------------------------------------------------------------
       |
       | Here is written all the status that can be a card application document.
       | That is used in the migration of the card application documents table.So
       | if you have already made the migration it is important to change also
       | the values in the database.
       |
       */

        'document' => [
            'status' => \App\Enum\CardDocumentStatusEnum::values(),
        ]
    ]
];
