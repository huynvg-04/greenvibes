<?php
return [
    'vnp_tmncode' => env('VNP_TMN_CODE', 'P1I4LRJR'),
    'vnp_hashsecret' => env('VNP_HASH_SECRET', '88Y6V45OJX1EFVLCYDNY4PJ2D9UCIXZ6'),
    'vnp_url' => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'vnp_returnurl' => env('VNP_RETURN_URL', 'http://yourdomain.com/payment-return'),
];
