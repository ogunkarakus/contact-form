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

	date_default_timezone_set($config['timezone']);

	if (isset($_GET['output']) && isset($_GET['output']['type']) && isset($_GET['file']))
	{
		header('Content-Type: application/json; charset=UTF-8', true, 200);

		print_r(json_decode(file_get_contents(urldecode($_GET['file']))));

		exit;
	}

	$files = glob(str_replace('\\', '/', __DIR__) . '/' . $config['log_dir_name'] . '/*.json');

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
		<meta content="noarchive,nofollow,noindex" name="robots" />
		<meta content="<?php echo $config['meta']['viewport']; ?>" name="viewport" />

		<title><?php echo $config['viewer']['title']; ?></title>

		<script charset="UTF-8" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.min.js"></script>
		<script charset="UTF-8" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script charset="UTF-8" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script charset="UTF-8" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.1.0/spin.min.js"></script>
		<script charset="UTF-8" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.1.0/ladda.min.js"></script>

		<style media="all" type="text/css">@font-face{font-family:Ubuntu;font-style:normal;font-weight:300;src:local('Ubuntu Light'),local('Ubuntu-Light'),url(https://fonts.gstatic.com/s/ubuntu/v6/e7URzK__gdJcp1hLJYAEag.woff) format('woff')}body{padding-bottom:25px;padding-top:25px}label{cursor:pointer}.input-group-addon{min-width:100px}.panel-body>.form-group:last-child{margin-bottom:0}.form-group>textarea{resize:none}.btn{background-color:transparent;background-repeat:no-repeat;border:none;border-radius:2.5px;min-width:100px;-o-transition:.5s;-ms-transition:.5s;-moz-transition:.5s;-webkit-transition:.5s;transition:.5s;text-align:center}.btn-default{border:1px solid #AAB2BD;color:#434A54}.btn-default:focus,.btn-default:hover{color:#FFF;background-color:#AAB2BD}input.form-control:focus,textarea.form-control:focus{border-color:#AAB2BD;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(170,178,189,.6);box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(170,178,189,.6)}.btn,.input-group-addon,.form-control,.panel,.modal-content,.alert{border-radius:0}.form-control,.form-group>label,.panel-title,.modal>*,.alert,.panel-footer{font-family:"Ubuntu",sans-serif;letter-spacing:1px}.input-group-addon{cursor:pointer}.btn-default[disabled],.btn-default[disabled]:focus{background-color:transparent;border-color:#AAB2BD}.panel-body{padding-bottom:0}.panel-footer>p{margin:0}</style>
	</head>
	<body>
		<div class="container-fluid" id="wrapper">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h1 class="panel-title"><?php echo $config['viewer']['title']; ?></h1>
					</div>
					<div class="panel-body"><?php if (count($files) <= 0) : echo PHP_EOL; ?>
						<div class="alert alert-info">
							<p><?php echo $config['viewer']['log_count_zero']; ?></p>
						</div><?php echo PHP_EOL; else : echo PHP_EOL; ?>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>#</th>
									<th><?php echo $config['fields']['name']; ?></th>
									<th><?php echo $config['fields']['email']; ?></th>
									<th><?php echo $config['fields']['ip']; ?></th>
									<th><?php echo $config['fields']['error_message']; ?></th>
									<th><?php echo $config['fields']['sended_at']; ?></th>
									<th><?php echo $config['fields']['browse']; ?></th>
								</tr>
							</thead>
							<tbody><?php foreach ($files as $index => $file) : $log = json_decode(file_get_contents($file)); echo PHP_EOL; ?>
								<tr>
									<td><?php echo $index + 1; ?></td>
									<td><?php echo $log->data->name; ?></td>
									<td><?php echo $log->data->email; ?></td>
									<td><?php echo $log->ip; ?></td>
									<td><?php echo strlen($log->phpmailer->errorInfo) > 0 ? $log->phpmailer->errorInfo : $config['viewer']['error_message_not_found']; ?></td>
									<td><?php echo date('d.m.Y H:i:s', filemtime($file)); ?></td>
									<td><a href="<?php echo 'viewer.php?output[type]=json&amp;file='.urlencode($file); ?>" target="_blank"><?php echo $config['fields']['browse']; ?></a></td>
								</tr><?php echo PHP_EOL; endforeach; ?>
							</tbody>
						</table><?php echo PHP_EOL; endif; ?>
					</div>
					<div class="panel-footer">
						<p class="text-center">Bu iletişim formu <a alt="author" href="http://ogunkarakus.com.tr" target="_blank" title="Ogün Karakuş"><strong>Ogün Karakuş</strong></a> tarafından hazırlanmıştır.</p>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
