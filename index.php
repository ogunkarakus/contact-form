<?php

    require_once 'vendor/autoload.php';

    use M1\Env\Parser as EnvParser;

    define( 'CONTACT_FORM', true );

    if ( ! file_exists( '.env' ) )
    {
        exit( '.env file does not exists.' );
    }

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

    $cdn = 'https://cdnjs.cloudflare.com/ajax/libs';

    $assets = [
        'scripts' => [
            'html5shiv' => $cdn . '/' . 'html5shiv/3.7.3/html5shiv.min.js',
            'jquery' => $cdn . '/' . 'jquery/2.2.4/jquery.min.js',
            'respond' => $cdn . '/' . 'respond.js/1.4.2/respond.min.js',
            'toastr' => $cdn . '/' . 'toastr.js/2.1.2/toastr.min.js',
        ],
        'styles' => [
            'font-awesome' => $cdn . '/' .
                              'font-awesome/4.6.3/css/font-awesome.min.css',
            'normalize' => $cdn . '/' . 'normalize/4.1.1/normalize.min.css',
            'toastr' => $cdn . '/' . 'toastr.js/2.1.2/toastr.min.css',
        ],
    ];

    $scripts = $assets[ 'scripts' ];
    $styles = $assets[ 'styles' ];

    $frm_input_address = $messages[ 'form' ][ 'input' ][ 'address' ];
    $frm_input_name = $messages[ 'form' ][ 'input' ][ 'name' ];
    $frm_input_subject = $messages[ 'form' ][ 'input' ][ 'subject' ];
    $frm_input_body = $messages[ 'form' ][ 'input' ][ 'body' ];

    $msg_error = $messages[ 'mail' ][ 'sent_error' ];
    $msg_success = $messages[ 'mail' ][ 'sent_success' ];

    $viewport = 'initial-scale=1, maximum-sca'.
                'le=1, user-scalable=no, widt'.
                'h=device-width';

?><!DOCTYPE html>
<html>
    <head>
        <link href="//cdnjs.cloudflare.com" rel="dns-prefetch" />
        <link href="assets/images/favicon.ico" rel="shortcut icon" />
        <link href="<?php echo $styles[ 'normalize' ]; ?>"
              rel="stylesheet" />
        <link href="<?php echo $styles[ 'font-awesome' ]; ?>"
              rel="stylesheet" />
        <link href="<?php echo $styles[ 'toastr' ]; ?>"
              rel="stylesheet" />
        <link href="assets/stylesheets/app.css" rel="stylesheet" />
        <meta charset="UTF-8" />
        <meta content="<?php echo $viewport; ?>" name="viewport" />
        <!--[if lt IE 9]>
            <script src="<?php echo $scripts[ 'html5shiv' ]; ?>"></script>
            <script src="<?php echo $scripts[ 'respond' ]; ?>"></script>
        <![endif]-->
        <script>
            var l10n = {
                "messages": {
                    "error": "<?php echo $msg_error; ?>",
                    "success": "<?php echo $msg_success; ?>"
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
                <form accept-charset="UTF-8"
                      action="<?php echo $form_action; ?>"
                      id="contact-form"
                      method="post">
                    <div class="form-item-group-item">
                        <label for="name">
                            <span><?php echo $frm_input_name; ?></span>
                            <span>:</span>
                        </label>
                        <input id="name"
                               name="from_name"
                               type="text"
                               required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="address">
                            <span><?php echo $frm_input_address; ?></span>
                            <span>:</span>
                        </label>
                        <input id="address"
                               name="from_address"
                               type="email"
                               required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="subject">
                            <span><?php echo $frm_input_subject; ?></span>
                            <span>:</span>
                        </label>
                        <input id="subject"
                               name="subject"
                               type="text"
                               required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="body">
                            <span><?php echo $frm_input_body; ?></span>
                            <span>:</span>
                        </label>
                        <textarea id="body"
                                  name="body"
                                  required="required"
                                  rows="5"></textarea>
                    </div>
                    <div class="form-item-group-item">
                        <button id="form-submit-button" type="submit">
                            <i class="fa fa-fw fa-send"></i>
                        </button>
                    </div>
                </form>
            </section>
        </div>
        <script src="<?php echo $scripts[ 'jquery' ]; ?>"></script>
        <script src="<?php echo $scripts[ 'toastr' ]; ?>"></script>
        <script src="assets/javascripts/app.js"></script>
    </body>
</html>
