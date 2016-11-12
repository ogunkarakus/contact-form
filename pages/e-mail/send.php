<?php

require_once '../../vendor/autoload.php';

use M1\Env\Parser as EnvParser;

header( 'Content-Type: application/json; charset=UTF-8' );

$is_ajax = isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] );

$ajax_header = $_SERVER[ 'HTTP_X_REQUESTED_WITH' ];

$is_ajax = $is_ajax ? ( $ajax_header == 'XMLHttpRequest' ) : false;

$success_false = json_encode( [ 'success' => false, ] );

$success_true = json_encode( [ 'success' => true, ] );

if ( ! $is_ajax )
{
    exit( $success_false );
}

$input_names = [ 'body', 'subject', 'from_address', 'from_name', ];

foreach ( $input_names as $input_name )
{
    if ( isset( $_POST[ $input_name ] ) && empty( $_POST[ $input_name ] ) )
    {
        exit( $success_false );
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
     mb_strlen( $data[ 'subject' ],
                mb_detect_encoding( $data[ 'subject' ] ) ) > 70 )
{
    exit( $success_false );
}

if ( ! filter_var( $data[ 'from' ][ 'address' ], FILTER_VALIDATE_EMAIL ) )
{
    exit( $success_false );
}

if ( ! is_string( $data[ 'from' ][ 'name' ] ) ||
     mb_strlen( $data[ 'from' ][ 'name' ],
                mb_detect_encoding( $data[ 'from' ][ 'name' ] ) ) > 70 )
{
    exit( $success_false );
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
        exit( $success_true );
    }
    else
    {
        exit( $success_false );
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
