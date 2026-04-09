<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Opening hours for trailer reservations
    |--------------------------------------------------------------------------
    |
    | Times are local application time (config('app.timezone')).
    |
    | - weekly: per ISO-8601 day of week (1=Mon ... 7=Sun)
    | - closures: specific dates that are fully closed (Y-m-d)
    | - special: specific dates overriding weekly hours (Y-m-d)
    |
    */

    'step_minutes' => 30,

    'weekly' => [
        1 => ['open' => '08:00', 'close' => '18:30'], // Monday
        2 => ['open' => '08:00', 'close' => '18:30'], // Tuesday
        3 => ['open' => '08:00', 'close' => '18:30'], // Wednesday
        4 => ['open' => '08:00', 'close' => '18:30'], // Thursday
        5 => ['open' => '08:00', 'close' => '18:30'], // Friday
        6 => ['open' => '08:00', 'close' => '14:00'], // Saturday
        7 => null, // Sunday closed
    ],

    'closures' => [
        '2025-12-25',
        '2025-12-26',
        '2026-01-01',
    ],

    'special' => [
        '2025-12-24' => ['open' => '08:00', 'close' => '14:00'],
        '2025-12-31' => ['open' => '08:00', 'close' => '14:00'],
    ],
];
