<?php

    require_once '../../vendor/autoload.php';

    use M1\Env\Parser as EnvParser;

    header( 'Content-Type: application/json; charset=UTF-8' );

    $is_ajax = isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] );

    $ajax_header = $_SERVER[ 'HTTP_X_REQUESTED_WITH' ];

    $is_ajax = $is_ajax ? ( $ajax_header == 'XMLHttpRequest' ) : false;

    if ( $is_ajax )
    {
        $data = [
            'body' => $_POST[ 'body' ],
            'subject' => $_POST[ 'subject' ],
            'to' => [
                'address' => $_POST[ 'to_address' ],
                'name' => $_POST[ 'to_name' ],
            ],
        ];

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

        // TODO: try-catch block
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
