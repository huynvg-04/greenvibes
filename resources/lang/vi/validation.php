<?php

return [
    'required' => 'Trường :attribute là bắt buộc.',
    'email' => 'Trường :attribute phải là địa chỉ email hợp lệ.',
    'unique' => 'Trường :attribute đã được sử dụng.',
    'max' => [
        'string' => 'Trường :attribute không được vượt quá :max ký tự.',
    ],
    'min' => [
        'numeric' => 'Trường :attribute phải ít nhất :min.',
        'string' => 'Trường :attribute phải có ít nhất :min ký tự.',
    ],

    'custom' => [
        'email' => [
            'unique' => 'Email này đã được sử dụng.',
        ],
    ],
];
