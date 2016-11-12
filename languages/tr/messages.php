<?php

if ( ! defined( 'CONTACT_FORM' ) )
{
    exit( 'Hacking?' );
}

return [
    'form' => [
        'input' => [
            'address' => 'E-posta Adresi',
            'body' => 'Mesaj',
            'name' => 'İsim',
            'subject' => 'Konu',
        ],
    ],
    'mail' => [
        'sent_error' => 'Mesaj gönderilirken hata oluştu.',
        'sent_success' => 'Mesaj başarıyla gönderildi.'
    ],
    'title' => 'İletişim',
];
