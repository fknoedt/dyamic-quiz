<?php
/**
 * index script for API requests
 */

// constant to globally identify the request type (public, sse, ajax, json)
define('REQUEST_TYPE','api');

try {

	// system general security and configuration settings
	require('../config/config.inc.php');

	// api only speaks json, check your network console
	error_reporting(0);

	// particular DynamicQuiz routes (extending Api\ApiRouter)
	$apiRoute = new DynamicQuiz\DynamicQuizRouter();

	// main controller (extends generic class Api\ApiController)
	$apiController = new Api\ApiController();

	// set debug for verbosity when not in prod
	\Api\ApiController::$appDebug = DEBUG_MODE;

	// handle request by the DynamicQuizRouter rules (Dependency Injection)
	$apiController->handleRequest($apiRoute);

	// execution shouldn't get to this point -- $apiController->sendResponse() exits
	exit;

}
catch(Exception $e) {

	$code = $e->getCode();

	// error array to be converted to json format
	$response = array(

		'errors' => array(
			'error' => array(
				'code' => $code,
				'message' => $e->getMessage(),
			)
		)

	);

	// always
	header('Content-Type: application/json charset=utf-8');

	// http status code
	header("HTTP/1.1 500 Internal Error", true, 500);

	// format as json object
	$json = json_encode($response);

	echo $json;

	exit;

}


