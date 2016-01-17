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

	require(str_replace('\\', '/', __DIR__) . '/lib/formatting.php');

	require(str_replace('\\', '/', __DIR__) . '/lib/EasyPeasyICS.php');

	require(str_replace('\\', '/', __DIR__) . '/lib/htmlfilter.php');

	require(str_replace('\\', '/', __DIR__) . '/lib/ntlm_sasl_client.php');

	require(str_replace('\\', '/', __DIR__) . '/lib/phpmailer/languages/phpmailer.lang-' . $config['language'] . '.php');

	require(str_replace('\\', '/', __DIR__) . '/lib/class.phpmailer.php');

	require(str_replace('\\', '/', __DIR__) . '/lib/class.pop3.php');

	require(str_replace('\\', '/', __DIR__) . '/lib/class.smtp.php');

	date_default_timezone_set($config['timezone']);

	function fieldToName($field)
	{
		global $config;

		return $config['fields'][$field];
	}

	function generateHtmlContent($data)
	{
		global $config;

		$html = file_get_contents(str_replace('\\', '/', __DIR__) . '/email/contents.html');

		$html = str_replace('{{ $email }}', $data['email'], $html);
		$html = str_replace('{{ $fields[\'email\'] }}', $config['fields']['email'], $html);
		$html = str_replace('{{ $fields[\'ip\'] }}', $config['fields']['ip'], $html);
		$html = str_replace('{{ $fields[\'message\'] }}', $config['fields']['message'], $html);
		$html = str_replace('{{ $fields[\'name\'] }}', $config['fields']['name'], $html);
		$html = str_replace('{{ $ip }}', getClientIp(), $html);
		$html = str_replace('{{ $message }}', wpautop($data['message']), $html);
		$html = str_replace('{{ $name }}', $data['name'], $html);
		$html = str_replace('{{ $subject }}', sprintf($config['mail']['subject'], $data['name']), $html);

		return $html;
	}

	function getClientIp()
	{
		if (getenv('HTTP_X_FORWARDED_FOR'))
		{
			return getenv('HTTP_X_FORWARDED_FOR');
		}

		if (getenv('HTTP_CLIENT_IP'))
		{
			return getenv('HTTP_CLIENT_IP');
		}

		return getenv('REMOTE_ADDR');
	}

	if (getenv('REQUEST_METHOD') == 'POST' && getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest')
	{
		header('Content-Type: application/json; charset=UTF-8', true, 200);

		foreach ($_POST as $key => $value)
		{
			if ($value == '')
			{
				$message[] = sprintf($config['messages']['required'], fieldToName($key));
				$status = false;
			}

			if ($key == 'email' && strlen(trim($value)) > 0 && filter_var($value, FILTER_VALIDATE_EMAIL) == false)
			{
				$message[] = sprintf($config['messages']['email'], fieldToName($key));
				$status = false;
			}

			if ($key == 'message' && strlen(trim($value)) <= 10 && strlen(trim($value)) > 0)
			{
				$message[] = sprintf($config['messages']['min_length_ten'], fieldToName($key));
				$status = false;
			}

			if ($key == 'name' && strlen(trim($value)) <= 2 && strlen(trim($value)) > 0)
			{
				$message[] = sprintf($config['messages']['min_length_two'], fieldToName($key));
				$status = false;
			}

			if ($key == 'captcha' && strlen(trim($value)) >= 7 && strlen(trim($value)) <= 0)
			{
				$message[] = sprintf($config['messages']['invalid_captcha'], fieldToName($key));
				$status = false;
			}

			if ($key == 'captcha' && strlen(trim($value)) > 0 && $_SESSION['captcha'] != trim($value))
			{
				$message[] = sprintf($config['messages']['invalid_captcha'], fieldToName($key));
				$status = false;
			}
		}

		if ( ! isset($status) && ! isset($message))
		{
			$mail = new PHPMailer();

			$mail->setLanguage($config['language'], str_replace('\\', '/', __DIR__) . '/lib/phpmailer/languages/');

			$mail->isSMTP();
			$mail->isHTML(true);

			$mail->SMTPAuth = true;

			$mail->Host = $config['smtp']['host'];
			$mail->Port = $config['smtp']['port'];
			$mail->SMTPSecure = $config['smtp']['type'];

			$mail->Username = $config['smtp']['username'];
			$mail->Password = $config['smtp']['password'];

			$mail->CharSet = $config['mail']['charset'];
			$mail->ContentType = $config['mail']['content_type'];

			$mail->setFrom($_POST['email'], $_POST['name']);
			$mail->addAddress($config['mail']['receiver']['email'], $config['mail']['receiver']['name']);

			$mail->Subject = sprintf($config['mail']['subject'], $_POST['name']);
			$mail->msgHTML(generateHtmlContent($_POST), str_replace('\\', '/', __DIR__) . '/email');

			if ( ! $mail->send())
			{
				file_put_contents(str_replace('\\', '/', __DIR__) . '/' . $config['log_dir_name'] . '/' . time() . '.json', json_encode(array('data' => $_POST, 'ip' => getClientIp(), 'phpmailer' => array('errorInfo' => $mail->ErrorInfo))));
			}

			$message[] = "<div class=\"alert alert-success\">{$config['messages']['success']}</div>";
			$status = true;
		}
		else
		{
			if (count($message) == 1 && $status == true)
			{
				$message = $message[0];
			}
			else
			{
				$_tmp = '<ul>';

				foreach ($message as $m)
				{
					$_tmp .= '<li>' . $m . '</li>';
				}

				$message = $_tmp . '</ul>';

				unset($m, $_tmp);
			}
		}
		
		exit(json_encode(array('message' => $message, 'r' => time() + rand(2, 7), 'status' => $status)));
	}

?><!DOCTYPE html>
<html class="no-js">
	<head>
		<link href="//cdnjs.cloudflare.com" rel="dns-prefetch" />
		<link href="//fonts.gstatic.com" rel="dns-prefetch" />

		<link href="favicon.ico" rel="icon" type="image/x-icon" />

		<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" />
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css" />
		<link href="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.1.0/ladda-themeless.min.css" media="all" rel="stylesheet" type="text/css" />

		<meta charset="UTF-8" />

		<meta content="Ogün Karakuş" name="author" />
		<meta content="<?php echo $config['meta']['description']; ?>" name="description" />
		<meta content="<?php echo $config['meta']['keywords']; ?>" name="keywords" />
		<meta content="<?php echo $config['meta']['robots']; ?>" name="robots" />
		<meta content="<?php echo $config['meta']['viewport']; ?>" name="viewport" />

		<title><?php echo $config['title']; ?></title>

		<script charset="UTF-8" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.min.js"></script>
		<script charset="UTF-8" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script charset="UTF-8" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script charset="UTF-8" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.1.0/spin.min.js"></script>
		<script charset="UTF-8" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.1.0/ladda.min.js"></script>

		<script type="text/javascript">$(function(){var r=<?php echo time() + rand(500, 1000); ?>;Ladda.bind("button[type=submit]"),$(".input-group-addon").on("click",function(){return $(this).next().focus(),!1}),$("div#wrapper form").on("submit",function(t){return t.preventDefault(),$.ajax({data:$(this).serialize(),url:document.URL,type:"POST"}).done(function(t){Ladda.stopAll(),$("h4").html(t.status?"<?php echo $config['modal']['titles']['success']; ?>":"<?php echo $config['modal']['titles']['failed']; ?>"),$(".modal-body").html(t.message),t.status===!0&&$(".modal-body").css("padding-bottom","0"),$("#messageModal").modal({show:!0});if(t.status){$("img[alt=captcha]").attr("src","captcha.php?r="+t.r)}}),!1})})</script>

		<style media="all" type="text/css">@font-face{font-family:Ubuntu;font-style:normal;font-weight:300;src:local('Ubuntu Light'),local('Ubuntu-Light'),url(https://fonts.gstatic.com/s/ubuntu/v6/e7URzK__gdJcp1hLJYAEag.woff) format('woff')}body{padding-bottom:25px;padding-top:25px}label{cursor:pointer}.input-group-addon{min-width:100px}.panel-body>.form-group:last-child{margin-bottom:0}.form-group>textarea{resize:none}.btn{background-color:transparent;background-repeat:no-repeat;border:none;border-radius:2.5px;min-width:100px;-o-transition:.5s;-ms-transition:.5s;-moz-transition:.5s;-webkit-transition:.5s;transition:.5s;text-align:center}.btn-default{border:1px solid #AAB2BD;color:#434A54}.btn-default:focus,.btn-default:hover{color:#FFF;background-color:#AAB2BD}input.form-control:focus,textarea.form-control:focus{border-color:#AAB2BD;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(170,178,189,.6);box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(170,178,189,.6)}.btn,.input-group-addon,.form-control,.panel,.modal-content,.alert{border-radius:0}.form-control,.form-group>label,.panel-title,.modal>*{font-family:"Ubuntu",sans-serif;letter-spacing:1px}.input-group-addon{cursor:pointer}.btn-default[disabled],.btn-default[disabled]:focus{background-color:transparent;border-color:#AAB2BD}</style>
	</head>
	<body>
		<div class="container-fluid" id="wrapper">
			<div class="col-xs-12">
				<form accept-charset="UTF-8" class="panel panel-default" method="POST">
					<div class="panel-heading">
						<h1 class="panel-title"><?php echo $config['title']; ?></h1>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="sr-only" for="name"><?php echo $config['labels']['name']; ?></label>
							<div class="input-group">
								<div class="input-group-addon"><i class="fa fa-user"></i></div>
								<input class="form-control" id="name" name="name" placeholder="<?php echo $config['labels']['name']; ?>" type="text" />
							</div>
						</div>
						<div class="form-group">
							<label class="sr-only" for="email"><?php echo $config['labels']['email']; ?></label>
							<div class="input-group">
								<div class="input-group-addon"><i class="fa fa-envelope"></i></div>
								<input class="form-control" id="email" name="email" placeholder="<?php echo $config['labels']['email']; ?>" type="text" />
							</div>
						</div>
						<div class="form-group">
							<label class="sr-only" for="message"><?php echo $config['labels']['message']; ?></label>
							<textarea class="form-control" id="email" name="message" placeholder="<?php echo $config['labels']['message']; ?>" rows="5"></textarea>
						</div>
						<div class="form-group">
							<label class="sr-only" for="captcha"><?php echo $config['labels']['captcha']; ?></label>
							<div class="input-group">
								<div class="input-group-addon"><img alt="captcha" title="<?php echo $config['labels']['captcha']; ?>" src="captcha.php?r=<?php echo time(); ?>" /></div>
								<input class="form-control" id="captcha" name="captcha" placeholder="<?php echo $config['labels']['captcha']; ?>" type="text" />
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<button class="btn btn-block btn-default ladda-button" data-spinner-color="#000000" data-style="zoom-out" type="submit"><span class="ladda-label"><i class="fa fa-send"></i></span></button>
					</div>
				</form>
			</div>
		</div>
		<div aria-hidden="true" aria-labelledby="messageModal" class="modal fade" id="messageModal" role="dialog" tabindex="-1">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo $config['labels']['close']; ?></span></button>
						<h4 class="modal-title" id="messageModalLabel"></h4>
					</div>
					<div class="modal-body"></div>
				</div>
			</div>
		</div>
	</body>
</html>
