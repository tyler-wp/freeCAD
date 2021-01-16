<?php
require '../../../connect.php';

require '../../../config.php';
// Set Content-Type to JSON
header('Content-Type: application/json');
// Check if API Endpoint is Valid
if(!isset($_GET['endpoint'])) {
    echo json_encode(array(
        'response' => 400,
        'content' => 'Missing Endpoint'
    ));
    exit();
}
switch(strtolower($_GET['endpoint'])) {
    case "vehicles":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM vehicles')
        ));
        break;
	case "characters":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM characters')
        ));
        break;
    default:
        echo json_encode(array(
            'response' => 400,
            'content' => 'Invalid Endpoint'
        ));
        break;
}