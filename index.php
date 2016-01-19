<?php

    require_once 'vendor/autoload.php';

    use M1\Env\Parser as EnvParser;

    $env = EnvParser::parse( file_get_contents( '.env' ) );

    $is_cli = strpos( php_sapi_name(), 'cli' ) !== false;

    $form_action = $is_cli ? 'pages/e-mail/send.php' : 'send';

    if ( file_exists( 'languages/' . $env[ 'APP_LOCALE' ] . '/messages.php' ) )
    {
        $messages = require(
            'languages/' . $env[ 'APP_LOCALE' ] . '/messages.php'
        );
    }
    else
    {
        $messages = require(
            'languages/' . $env[ 'APP_LOCALE_FALLBACK' ] . '/messages.php'
        );
    }

?><!DOCTYPE html>
<html>
    <head>
        <link href="assets/images/favicon.ico" rel="icon" />
        <link href="assets/stylesheets/normalize.min.css" rel="stylesheet" />
        <link href="assets/stylesheets/font-awesome.min.css" rel="stylesheet" />
        <link href="assets/stylesheets/toastr.min.css" rel="stylesheet" />
        <link href="assets/stylesheets/app.css" rel="stylesheet" />
        <meta charset="UTF-8" />
        <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport" />
        <!--[if lt IE 9]>
            <script src="assets/javascripts/html5shiv.min.js"></script>
            <script src="assets/javascripts/respond.min.js"></script>
        <![endif]-->
        <script>
            var l10n = {
                "messages": {
                    "error": "<?php echo $messages[ 'mail' ][ 'sent_error' ]; ?>",
                    "success": "<?php echo $messages[ 'mail' ][ 'sent_success' ]; ?>"
                }
            };
        </script>
        <title><?php echo $messages[ 'title' ]; ?></title>
    </head>
    <body>
        <div id="app">
            <section id="contact-form-section">
                <h1><?php echo $messages[ 'title' ]; ?></h1>
                <hr />
                <form accept-charset="UTF-8" action="<?php echo $form_action; ?>" id="contact-form" method="post">
                    <div class="form-item-group-item">
                        <label for="name">
                            <span><?php echo $messages[ 'form' ][ 'input' ][ 'name' ]; ?></span>
                            <span>:</span>
                        </label>
                        <input id="name" name="to_name" type="text" required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="address">
                            <span><?php echo $messages[ 'form' ][ 'input' ][ 'address' ]; ?></span>
                            <span>:</span>
                        </label>
                        <input id="address" name="to_address" type="email" required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="subject">
                            <span><?php echo $messages[ 'form' ][ 'input' ][ 'subject' ]; ?></span>
                            <span>:</span>
                        </label>
                        <input id="subject" name="subject" type="text" required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="body">
                            <span><?php echo $messages[ 'form' ][ 'input' ][ 'body' ]; ?></span>
                            <span>:</span>
                        </label>
                        <textarea id="body" name="body" required="required" rows="5"></textarea>
                    </div>
                    <div class="form-item-group-item">
                        <button id="form-submit-button" type="submit">
                            <i class="fa fa-fw fa-send"></i>
                        </button>
                    </div>
                </form>
            </section>
        </div>
        <script src="assets/javascripts/jquery.min.js"></script>
        <script src="assets/javascripts/toastr.min.js"></script>
        <script src="assets/javascripts/app.js"></script>
    </body>
</html>
