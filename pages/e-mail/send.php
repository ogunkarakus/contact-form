<?php

    $is_ajax = isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] );

    if ( $is_ajax )
        $is_ajax = $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] == 'XMLHttpRequest';

    if ( $is_ajax )
    {
        $name = $_POST[ 'name' ];
        $e_mail_address = $_POST[ 'e-mail-address' ];
        $message = $_POST[ 'message' ];
    }
