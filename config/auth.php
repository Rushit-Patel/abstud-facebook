<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'student' => [
            'driver' => 'session',
            'provider' => 'students',
        ],
        'partner' => [
            'driver' => 'session',
            'provider' => 'partners',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'username_eloquent',
            'model' => App\Models\User::class,
        ],
        'students' => [
            'driver' => 'eloquent',
            'model' => App\Models\Student::class,
        ],
        'partners' => [
            'driver' => 'eloquent',
            'model' => App\Models\Partner::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'students' => [
            'provider' => 'students',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'partners' => [
            'provider' => 'partners',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];