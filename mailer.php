<?php

require 'vendor/autoload.php';
require 'config.php';

/*
 * Setup
 */
$database = new medoo($config['database']);
$mandrill = new Mandrill($config['mandrill']['api_key']);
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);
$emailHTML = $twig->loadTemplate('email.html.twig');
$emailText = $twig->loadTemplate('email.text.twig');

/*
 * Gather all entries to sent
 */
$entries = $database->select('people', ['uuid', 'name', 'email', 'company_name'], ['company_name[!]' => null, 'GROUP' => 'email']);

/*
 * Queue all emails
 */
foreach ($entries as $entry) {

    // Compose message
    $data = [
        'company' => $entry['company_name'],
        'uuid'    => $entry['uuid'],
        'name'    => $entry['name']
    ];
    $message = [
        'html'         => $emailHTML->render($data),
        'text'         => $emailText->render($data),
        'subject'      => 'Research project Eclipse ecosystem: confirmation of your employer is requested',
        'from_email'   => 'jakob@jakobbuis.nl',
        'from_name'    => 'Jakob Buis',
        'to'           => [['email' => $entry['email']]],
        'track_opens'  => false,
        'track_clicks' => false
    ];

    // Send email
    $result = $mandrill->messages->send($message, false);

    // Return result
    echo 'Email sent to ID ' . $entry['uuid'] . ' email ' . $entry['email'] . ' status ' . $result[0]['status'] . "\n";
}
