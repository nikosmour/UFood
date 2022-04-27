<?php
return [
    /*
   |--------------------------------------------------------------------------
   | User Status
   |--------------------------------------------------------------------------
   |
   | Here is written all the status that can be a user.That is used in the
   | migration of the users table.So if you have already made the migration
   | it is important to change also the values in the database.
   |
   */
    'status'=>\App\Enum\UserStatusEnum::values()
];
