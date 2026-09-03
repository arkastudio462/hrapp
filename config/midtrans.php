<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'production' => env('MIDTRANS_PRODUCTION', false),
    'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap'),
    'api_url' => env('MIDTRANS_API_URL', 'https://api.sandbox.midtrans.com'),
];
