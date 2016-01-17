<?php

	$config = array(

		'fields' => array(

			'browse' => 'Gözat',
			'captcha' => 'Güvenlik kodu',
			'email' => 'E-posta',
			'error_message' => 'Hata mesajı',
			'ip' => 'IP adresi',
			'message' => 'Mesaj',
			'name' => 'İsim',
			'sended_at' => 'Gönderilme zamanı',

		),

		'labels' => array(

			'captcha' => 'Güvenlik kodu',
			'close' => 'Kapat',
			'email' => 'E-posta adresiniz',
			'message' => 'Mesajınız',
			'name' => 'İsminiz',

		),

		'language' => 'tr',

		'log_dir_name' => 'log',

		'mail' => array(

			'charset' => 'UTF-8',
			'content_type' => 'text/html',

			'receiver' => array(

				'email' => '', # Alıcı e-posta adresi
				'name' => '', # Alıcının adı ve soyadı

			),

			'subject' => '%s Size Mesaj Gönderdi!',

		),

		'meta' => array(

			'description' => '',
			'keywords' => '',
			'robots' => '',
			'viewport' => '',

		),

		'messages' => array(

			'email' => '%s alanına geçerli bir e-posta adresi girilmelidir.',
			'invalid_captcha' => '%s doğru girilmedi.',
			'min_length_ten' => '%s alanı en az on karakterden oluşmalıdır.',
			'min_length_two' => '%s alanı en az iki karakterden oluşmalıdır.',
			'required' => '%s alanı boş bırakılamaz.',
			'success' => 'Mesajınız başarıyla gönderildi. En kısa sürede e-posta adresinize mesajınız cevaplanacaktır.',

		),

		'modal' => array(

			'titles' => array(

				'failed' => 'Başarısız',
				'success' => 'Başarılı',

			),

		),

		'timezone' => 'Europe/Istanbul',

		'title' => 'İletişim Formu',

		'smtp' => array(

			'host' => 'smtp.gmail.com',
			'port' => 587,
			'type' => 'tls',

			'username' => '',
			'password' => '',

		),

		'viewer' => array(

			'error_message_not_found' => 'Hata mesajı yok!',
			'log_count_zero' => 'Müthiş! Hiçbir hata raporu bulunamadı. İletişim formu sorunsuzca çalışıyor.',

			'title' => 'Hata Raporu İzleyicisi',

		),

	);
