<?php

    require_once '../../vendor/autoload.php';

    use M1\Env\Parser as EnvParser;

    header( 'Content-Type: application/json; charset=UTF-8' );

    $is_ajax = isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] );

    $ajax_header = $_SERVER[ 'HTTP_X_REQUESTED_WITH' ];

    $is_ajax = $is_ajax ? ( $ajax_header == 'XMLHttpRequest' ) : false;

    if ( $is_ajax )
    {
        $input_names = [
            'body', 'subject', 'from_address', 'from_name',
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
            'from' => [
                'address' => $_POST[ 'from_address' ],
                'name' => $_POST[ 'from_name' ],
            ],
            'subject' => $_POST[ 'subject' ],
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

        if ( ! filter_var( $data[ 'from' ][ 'address' ],
                           FILTER_VALIDATE_EMAIL ) )
        {
            exit( json_encode( [
                'success' => false,
            ] ) );
        }

        if ( ! is_string( $data[ 'from' ][ 'name' ] ) ||
             mb_strlen(
                 $data[ 'from' ][ 'name' ],
                 mb_detect_encoding( $data[ 'from' ][ 'name' ] )
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

        $loader = new Twig_Loader_Filesystem( '../../templates' );

        $twig = new Twig_Environment( $loader, [
            'autoescape' => true,
            'cache' => false,
        ] );

        $template = $twig->loadTemplate( $env[ 'TWIG_TEMPLATE_FILE_NAME' ] );

        $body = $template->render( $data );

        $message = Swift_Message::newInstance();

        $message->setBody( $body, 'text/html' );

        $message->setTo( [
            $env[ 'SMTP_TO_ADDRESS' ] => $env[ 'SMTP_TO_NAME' ]
        ] );

        $message->setSubject( $data[ 'subject' ] );

        $message->setFrom( [
            $data[ 'from' ][ 'address' ] => $data[ 'from' ][ 'name' ],
        ] );

        try
        {
            $is_sended = $mailer->send( $message );

            if ( $is_sended )
            {
                exit( json_encode( [
                    'success' => true,
                ] ) );
            }
            else
            {
                exit( json_encode( [
                    'success' => false,
                ] ) );
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
    else
    {
        exit( json_encode( [
            'success' => false,
        ] ) );
    }
