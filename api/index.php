<?php

// dependencies

require_once(dirname(__FILE__) . '/inc/config.php');
require_once(dirname(__FILE__) . '/inc/api_response.php');
require_once(dirname(__FILE__) . '/inc/api_logic.php');
require_once(dirname(__FILE__) . '/inc/database.php');

// ----------------------------------------------------------
// instantiate the api_class
$api_response = new api_response();

// ----------------------------------------------------------
//check if method is valid
if (!$api_response->check_method(($_SERVER['REQUEST_METHOD']))) {
    //send error response
    $api_response->api_request_error('Invalid request method.');
}

// ----------------------------------------------------------
// set request method
$api_response->set_method($_SERVER['REQUEST_METHOD']);
$params = null;
if ($api_response->get_method() == 'GET') {
    $api_response->set_endpoint($_GET['endpoint']);
    $params = $_GET;
} elseif ($api_response->get_method() == 'POST') {
    $api_response->set_endpoint($_POST['endpoint']);
    $params = $_POST;
}


// ----------------------------------------------------------
// prepare the api_logic
$api_logic = new api_logic($api_response->get_endpoint(), $params);

// ----------------------------------------------------------
// check if endpoint exists
if (!$api_logic->endpoint_exists()) {
    // send error response
    $api_response->api_request_error('Endpoint does not exist.' . $api_response->get_endpoint());
}

// ----------------------------------------------------------
// request to the api_logic
$result = $api_logic->{$api_response->get_endpoint()}();
$api_response->add_to_data('data', $result);

$api_response->send_response();

// $api_response->send_api_status();
