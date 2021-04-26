<?php

return [
    'commands' => [
        'driver_dir' => 'Log/Drivers/',
        'processor_dir' => 'Log/Processors/'
    ],
    'db' => [
        'channel' => 'db',
        'target' => [
            'select' => true,
            'update' => true,
            'delete' => true,
            'insert' => true,
        ],
        'explain' => false,
        'transaction' => false
    ]
];
