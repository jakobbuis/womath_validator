<?php

require 'vendor/autoload.php';

/*
 * Setup
 */
$database = new medoo($config['database']);
$mandrill = new Mandrill($config['mandrill']['api_key']);
$twig = new Twig();

/*
 * Gather all entries to sent
 */
$entries = $database->select('people', ['uuid', 'name', 'email', 'company_name'], ['company_name[!]' => null]);

/*
 * Queue all emails
 */
foreach ($entries as $entry) {

    // Compose message
    $data = [
        'company' => $entry['uuid'],
        'uuid'    => $entry['company_name'],
        'name'    => $entry['name']
    ];
    $message = [
        'html'       => $twig->render('email.html.twig', $data),
        'text'       => $twig->render('email.text.twig', $data),
        'subject'    => 'Research project Eclipse ecosystem: confirmation of your employer is requested',
        'from_email' => 'jakob@jakobbuis.nl',
        'from_name'  => 'Jakob Buis',
        'to'         => [['email' => $entry['email']]]
    ];

    // Send email
    $result = $mandrill->messages->send($message, false, 'Main pool', 'example send_at');

    // Return result
    echo 'Email sent to ID ' . $entry['uuid'] . ' email ' . $entry['email'] . ' status ' . $result['status'] . "\n";
}
