<?php

    $is_cli = strpos( php_sapi_name(), 'cli' ) !== false;

    $form_action = $is_cli ? 'pages/e-mail/send.php' : 'send';

?><!DOCTYPE html>
<html>
    <head>
        <link href="assets/images/favicon.ico" rel="icon" />
        <link href="assets/stylesheets/normalize.min.css" rel="stylesheet" />
        <link href="assets/stylesheets/font-awesome.min.css" rel="stylesheet" />
        <link href="assets/stylesheets/app.css" rel="stylesheet" />
        <meta charset="UTF-8" />
        <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport" />
        <title>Contact Form</title>
    </head>
    <body>
        <div id="app">
            <section id="contact-form-section">
                <h1>Contact Form</h1>
                <hr />
                <form accept-charset="UTF-8" action="<?php echo $form_action; ?>" id="contact-form" method="post">
                    <div class="form-item-group-item">
                        <label for="name">
                            <span>Name</span>
                            <span>:</span>
                        </label>
                        <input id="name" name="to_name" type="text" required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="address">
                            <span>E-mail Address</span>
                            <span>:</span>
                        </label>
                        <input id="address" name="to_address" type="email" required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="subject">
                            <span>Subject</span>
                            <span>:</span>
                        </label>
                        <input id="subject" name="subject" type="text" required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="body">
                            <span>Message</span>
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
        <script src="assets/javascripts/app.js"></script>
    </body>
</html>
