<?php

return [
    'providers' => [
        'ldapusers' => [
            'driver' => 'ldapeloquent',
            'model' => App\User::class,
        ],
    ],
];
