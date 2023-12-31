<?php

return [

    '100' => 'continue',
    '101' => 'switching protocols',
    '102' => 'processing ',
    '103' => 'early hints ',

    '200' => 'ok',
    '201' => 'created',
    '202' => 'accepted',
    '203' => 'non-authoritative ',
    '204' => 'no content',

    '307' => 'temporary redirect ',
    '308' => 'permanent redirect',

    '400' => 'bad Request',
    '401' => 'unauthorized',
    '402' => 'payment required',
    '403' => 'forbidden',
    '404' => 'not found',
    '405' => 'method not allowed',
    '406' => 'not acceptable',
    '408' => 'request timeout',
    '429' => 'too many requests',

    '500' => 'internal server error',
    '501' => 'not implemented',
    '502' => 'bad gateway',
    '503' => 'service unavailable',
    '504' => 'gateway timeout',
    
    'validation' =>[
        'error' => 'validation error'
    ]
];

?>