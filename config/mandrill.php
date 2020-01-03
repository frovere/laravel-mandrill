<?php

return [
    'secret' => env('MANDRILL_SECRET'),
    'options' => [
        'async' => env('MANDRILL_ASYNC'),
        'ip_pool' => env('MANDRILL_IP_POOL'),
    ],
];
