<?php

return [
    'driver' => env('MAIL_DRIVER', 'smtp'),
    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
    'port' => env('MAIL_PORT', 587),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'sendmail' => '/usr/sbin/sendmail -bs',
    'pretend' => false,
    'from' => ['address' => env('MAIL_USERNAME'), 'name' => env('MAIL_NAME')],
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
];
