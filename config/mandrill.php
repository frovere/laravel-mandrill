<?php

return [
    'scheme' => env('MANDRILL_SCHEME'),
    'secret' => env('MANDRILL_SECRET'),
    'options' => [
        'async' => env('MANDRILL_ASYNC'),
        'ip_pool' => env('MANDRILL_IP_POOL'),
    ],
];
