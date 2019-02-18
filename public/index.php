<?php
/**
 * index / controller for main (page) browser requests
 * @see https://github.com/fknoedt/dynamic-quiz
 */


//phpinfo();
//exit;

// constant to globally identify the request type (public, sse, ajax)
define('REQUEST_TYPE','www');

try {

	// system security and configuration
	require('../config/config.inc.php');

	// creates new template object
	$objTpl = new \Common\Template(VIEWS_DIR);

	//
	$objTpl->addVar('foo','bar');

	// parses template and converts variables
	$objTpl->display('index.tpl.php');

	// \Common\Lib::notifyVisit();


}
// TODO: personalize exception handlings
catch(\Exception\ConfigException $e) {

	$errorStatus = 'error';
	$errorMessage = "Config: ". $e->getMessage();

}
catch(\Exception\DatabaseException $e) {

	$errorStatus = 'error';
	$errorMessage = "DB: ". $e->getMessage();

	if(\Database::getConnection())
		$errorMessage .= " -- last query: " . \Database::getLastQuery();

}
catch(\Exception\FunctionalError $e) {

	$errorStatus = 'warning';
	$errorMessage = $e->getMessage();

}
catch(Exception $e) {

	$errorStatus = 'error';
	$errorMessage = $e->getMessage();

}

// some exception occurred
if(! empty($errorStatus)) {

	// if the error happened before the response array initiation
	if(! isset($aResponse))
		$aResponse = array(
			'status'	=> '',
			'action'	=> '',
			'errorMsg'	=> ''
		);

	// exception: shows label
	if($errorStatus == 'error')
		$message = 'Internal Error =X' . (defined('DEBUG_MODE') && DEBUG_MODE ? " [{$e->getMessage()}]" : '');
	// functional error: shows it to the user
	else
		$message = $e->getMessage();

	$aResponse['status'] = $errorStatus;
	$aResponse['action'] = @$_POST['action'];
	$aResponse['errorMsg'] = $message;

	// TODO: template for displaying error
	echo $message;

}
