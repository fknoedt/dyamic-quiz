<?php

namespace Api;
use Ratchet\Wamp\Exception;

/**
 * Class ApiController
 * Generic API Controllers to be extended by a particular controller
 * @TODO: version, enhance security, ...
 * @package Api
 */
class ApiController
{
    /**
     * only one ApiController should be ever instantiated
     * @var
     */
    static $singleton;

    /**
     * parse_url array of the URL being handled
     * @var
     */
    var $aUrl;

    /**
     * debug mode
     * @var bool
     */
    static $appDebug = false;

    /**
     * ApiController constructor.
     * @param $aUrl
     * @throws Exception
     */
    public function __construct($aUrl=null)
    {
        if(is_object(self::$singleton)) {
            return self::$singleton;
        }

        $this->aUrl = $aUrl;

        self::$singleton = $this;
    }


    /**
     * is request over SSL?
     * @return bool
     */
    public function sslRequest()
    {
        return (bool) ! (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off");
    }

    /**
     * gett full request with protocol, host, path, query string and fragments
     * @return string
     */
    public function getCurrentRequest()
    {
        return 'http' . (SSL_HOST ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * read POST data from
     * $_POST (x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type in the request)
     * or
     *  php://input (request body)
     * @return array|mixed
     */
    public static function getPostData() {

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            if(! empty($_POST)) {
                $postArgs = $_POST;
            }
            else {

                $requestBody = file_get_contents('php://input');

                if(is_object($requestBody)) {
                    $postArgs = (Array) $requestBody;
                }
                else {
                    $postArgs = json_decode($requestBody, true);
                }

            }

            return $postArgs;

        }
    }

    /**
     * validate and clear any request path
     * @param $url
     * @return string
     * @throws ApiException
     */
    public function parseRequestPath($url)
    {
        $this->aUrl = parse_url($url);

        $aPath = explode('/', $this->aUrl['path']);

        // first element will be ''
        unset($aPath[0]);

        // request has to start with /api
        if(! isset($aPath[1]) || $aPath[1] != 'api' ) {
            throw new ApiException('wrong path: has to start with /api', 400); // 400 - BAD REQUEST
        }

        // remove /api part of the url path
        unset($aPath[1]);

        // request has to have some path beyond /api
        if(count($aPath) == 0) {
            throw new ApiException('wrong path: some path is required', 400); // 400 - BAD REQUEST
        }

        return implode('/', $aPath);
    }

    /**
     * handle the current request by parsing the url generically and calling the particular ApiRequest method to handle the path
     * @TODO callback function should be able to return array with http status code
     * @param ApiRouter $apiRouter
     */
    public function handleRequest(ApiRouter $apiRouter)
    {
        try {

            // get request info
            $requestUrl = $this->getCurrentRequest();

            // get the clean path
            $path = $this->parseRequestPath($requestUrl);

            // get the response from the controller associated to the /api path
            $response = $apiRouter->route($path);

            // @TODO array response with different http status code

            $this->sendResponse($response);

        }
        catch(ApiException $e) {
            $this->handleError($e);
        }
        catch(Exception $e) {
            $this->handleError($e);
        }

    }

    /**
     * handle any API exception
     * @param \Exception $e
     * @throws ApiException
     */
    public function handleError(\Exception $e)
    {
        // if code not set, internal error (500)
        $code = isset(Api::$HTTP_CODES[$e->getCode()]) ? $e->getCode() : 500;

        $meta = [];

        if(self::$appDebug) {
            $meta = ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()];
        }

        $this->sendResponse($e->getMessage(), $code, $meta);
    }

    /**
     * send a REST/JSON response
     * @param $response
     * @param int $httpStatus
     * @throws ApiException
     */
    public function sendResponse($response, $httpStatus=200, $meta=[])
    {
        if(is_string($response)) {
            $response = ['data' => $response];
            if(!empty($meta)) {
                $response['meta'] = $meta;
            }
        }

        // convert response into json
        $jsonResponse = json_encode($response);

        // couldn't convert: error if not responding an Exception
        if(empty($jsonResponse)) {

            // TODO: test caller method to avoid infinite loop

            // response was ok: error
            if($httpStatus == 200) {
                throw new ApiException("invalid response: {$response}", 500); // internal server error
            }
            else {
                $jsonResponse = json_encode(['error' => 'Internal Error']);
            }

        }

        // unkown http status code: 500 Internal Error
        if(! in_array($httpStatus, array_keys(Api::$HTTP_CODES))) {
            throw new ApiException('invalid http status code: ' . $httpStatus, 500);
        }

        // always
        header('Content-Type: application/json charset=utf-8');

        // http status code
        header("HTTP/1.1 {$httpStatus} " . Api::$HTTP_CODES[$httpStatus], true, $httpStatus);

        // dump JSON
        echo $jsonResponse;

        exit;

    }
}