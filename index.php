<?php

require 'vendor/autoload.php';
require 'config.php';

/*
 * Setup and configuration
 */
$app = new \Slim\Slim();
$database = new medoo($config['database']);
/*
 * Validation endpoints
 */
$app->get('/hello/:uuid/correct', function ($name) {
    // ID must exist
    // Store answer is correct
    // Say thank you
});

$app->get('/hello/:uuid/wrong', function ($name) {
    // ID must exist
    // Store answer is wrong
    // Say thank you
});

// Start the application
$app->run();
