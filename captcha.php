<?php

	function is_session_started()
	{
		if (php_sapi_name() !== 'cli')
		{
			if (version_compare(phpversion(), '5.4.0', '>='))
			{
				return session_status() === PHP_SESSION_ACTIVE;
			}
			else
			{
				return session_id() !== '';
			}
		}

		return false;
	}

	if ( ! is_session_started())
	{
		session_start();
	}

	ini_set('html_errors', false);

	require(str_replace('\\', '/', __DIR__) . '/config.php');

	if ( ! isset($_GET['r']))
	{
		header('Content-Type: text/plain; charset=UTF-8', true, 403);

		exit;
	}

	header('Content-Type: image/png; charset=UTF-8', true, 200);

	$image = imagecreatetruecolor(80, 20);

	imagealphablending($image, false);

	$color = imagecolorallocatealpha($image, 255, 255, 255, 127);

	imagefilledrectangle($image, 0, 0, 80, 20, $color);

	imagealphablending($image, true);

	$font = str_replace('\\', '/', __DIR__) . '/fonts/Ubuntu-B.ttf';

	$color = imagecolorallocate($image, 67, 74, 84);

	$captcha = rand(0, 9) . ' ' . rand(0, 9) . ' ' . rand(0, 9) . ' ' . rand(0, 9) . ' ' . rand(0, 9) . ' ' . rand(0, 9);

	imagettftext($image, 10, 0, 7.5, 15.5, $color, $font, $captcha);

	$_SESSION['captcha'] = preg_replace('/\s+/', '', $captcha);

	imagealphablending($image, false);

	imagesavealpha($image, true);

	imagepng($image);

	exit;
