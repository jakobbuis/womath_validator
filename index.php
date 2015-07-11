<?php

require 'vendor/autoload.php';
require 'config.php';

/*
 * Setup and configuration
 */
$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig()
));
$app->view()->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);
$database = new medoo($config['database']);

/*
 * Go away message for stray users
 */
$app->get('/', function(){
    echo 'This is the validation endpoint for the womath research project.';
    echo 'You need to use the link in the email to validate.';
})->name('root');

/*
 * Validation endpoints
 */
$app->get('/:uuid/correct', function ($uuid) use ($app, $database) {
    // ID must exist
    if ($database->count('people', ['uuid' => $uuid]) !== 1) {
        return $app->render('error.twig');
    }

    // Mark entry as correct
    $database->update('people', ['validation' => 'correct'], ['uuid' => $uuid]);

    // Say thank you
    $company = $database->select('people', ['company_name'], ['uuid' => $uuid])[0]['company_name'];
    return $app->render('correct.twig', ['company' => $company, 'uuid' => $uuid]);

})->name('correct');

$app->get('/:uuid/wrong', function ($uuid) use ($app, $database) {
    // ID must exist
    if ($database->count('people', ['uuid' => $uuid]) !== 1) {
        return $app->render('error.twig');
    }

    // Mark entry as wrong
    $database->update('people', ['validation' => 'wrong'], ['uuid' => $uuid]);

    // Say thank you
    $company = $database->select('people', ['company_name'], ['uuid' => $uuid])[0]['company_name'];
    return $app->render('wrong.twig', ['company' => $company, 'uuid' => $uuid]);

})->name('wrong');

// Start the application
$app->run();
