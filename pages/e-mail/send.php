<?php

    require_once '../../vendor/autoload.php';

    use M1\Env\Parser as EnvParser;

    header( 'Content-Type: application/json; charset=UTF-8' );

    $is_ajax = isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] );

    $ajax_header = $_SERVER[ 'HTTP_X_REQUESTED_WITH' ];

    $is_ajax = $is_ajax ? ( $ajax_header == 'XMLHttpRequest' ) : false;

    if ( $is_ajax )
    {
        // TODO: "body" filter ( XSS etc. )
        $input_names = [
            'body', 'subject', 'to_address', 'to_name',
        ];

        foreach ( $input_names as $input_name )
        {
            if ( isset( $_POST[ $input_name ] ) &&
                 empty( $_POST[ $input_name ] )
               )
            {
                exit( json_encode( [
                    'success' => false,
                ] ) );
            }
        }

        $data = [
            'body' => $_POST[ 'body' ],
            'subject' => $_POST[ 'subject' ],
            'to' => [
                'address' => $_POST[ 'to_address' ],
                'name' => $_POST[ 'to_name' ],
            ],
        ];

        if ( ! is_string( $data[ 'subject' ] ) ||
             mb_strlen(
                 $data[ 'subject' ],
                 mb_detect_encoding( $data[ 'subject' ] )
             ) > 70
           )
        {
            exit( json_encode( [
                'success' => false,
            ] ) );
        }

        if ( ! filter_var( $data[ 'to' ][ 'address' ], FILTER_VALIDATE_EMAIL ) )
        {
            exit( json_encode( [
                'success' => false,
            ] ) );
        }

        if ( ! is_string( $data[ 'to' ][ 'name' ] ) ||
             mb_strlen(
                 $data[ 'to' ][ 'name' ],
                 mb_detect_encoding( $data[ 'to' ][ 'name' ] )
             ) > 70
           )
        {
            exit( json_encode( [
                'success' => false,
            ] ) );
        }

        $env = EnvParser::parse( file_get_contents( '../../.env' ) );

        $transport = Swift_SmtpTransport::newInstance();

        $transport->setHost( $env[ 'SMTP_HOST' ] )
                  ->setPort( $env[ 'SMTP_PORT' ] )
                  ->setEncryption( $env[ 'SMTP_ENCRYPTION' ] )
                  ->setUsername( $env[ 'SMTP_USER' ] )
                  ->setPassword( $env[ 'SMTP_PASS' ] );

        $mailer = Swift_Mailer::newInstance( $transport );

        $message = Swift_Message::newInstance();

        // TODO: HTML templating...
        $message->setBody( $data[ 'body' ], 'text/html' );

        $message->setFrom( [
            $env[ 'SMTP_FROM_ADDRESS' ] => $env[ 'SMTP_FROM_NAME' ]
        ] );

        $message->setSubject( $data[ 'subject' ] );

        $message->setTo( [
            $data[ 'to' ][ 'address' ] => $data[ 'to' ][ 'name' ]
        ] );

        try
        {
            $is_sended = $mailer->send( $message );

            if ( $is_sended )
            {
                // Mail sent!
            }
            else
            {
                // Something went wrong!
            }
        }
        catch ( Exception $exception )
        {
            exit( json_encode( [
                'success' => false,
                'error' => [
                    'message' => $exception->getMessage(),
                ],
            ] ) );
        }
    }
