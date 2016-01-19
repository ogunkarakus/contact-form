<?php

    header( 'Content-Type: application/json; charset=UTF-8' );

    $is_ajax = isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] );

    $ajax_header = $_SERVER[ 'HTTP_X_REQUESTED_WITH' ];

    $is_ajax = $is_ajax ? ( $ajax_header == 'XMLHttpRequest' ) : false;

    if ( $is_ajax )
    {
        $name = $_POST[ 'name' ];
        $e_mail_address = $_POST[ 'e-mail-address' ];
        $message = $_POST[ 'message' ];

        //
    }
