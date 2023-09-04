<?php

return [
    'autoload' => false,
    'hooks' => [
        'app_init' => [
            'qrcode',
        ],
    ],
    'route' => [
        '/qrcode$' => 'qrcode/index/index',
        '/qrcode/build$' => 'qrcode/index/build',
    ],
    'priority' => [],
    'domain' => '',
];
