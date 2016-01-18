<!DOCTYPE html>
<html>
    <head>
        <link href="favicon.ico" rel="icon" />
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
                <form accept-charset="UTF-8" action="send" id="contact-form" method="post">
                    <div class="form-item-group-item">
                        <label for="name">
                            <span>Name</span>
                            <span>:</span>
                        </label>
                        <input id="name" name="name" type="text" required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="e-mail-address">
                            <span>E-mail Address</span>
                            <span>:</span>
                        </label>
                        <input id="e-mail-address" name="e-mail-address" type="email" required="required" />
                    </div>
                    <div class="form-item-group-item">
                        <label for="message">
                            <span>Message</span>
                            <span>:</span>
                        </label>
                        <textarea id="message" name="message" required="required" rows="5"></textarea>
                    </div>
                    <div class="form-item-group-item">
                        <button type="submit">
                            <i class="fa fa-fw fa-send"></i>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </body>
</html>
