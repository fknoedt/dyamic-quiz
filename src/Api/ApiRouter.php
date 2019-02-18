<?php

namespace Api;

Class ApiRouter
{
    /**
     * handle the given path with the $aRoute settings
     * @param $path
     * @param $aRoute
     * @param null $requestMethod
     * @return mixed|string
     * @throws ApiException
     * @throws \Exception
     */
    public function handleRoute($path, $aRoute, $requestMethod=null)
    {
        $response = '';

        // the current request method (GET, POST, PUT, etc) is the default
        if(! $requestMethod) {
            $requestMethod = $_SERVER['REQUEST_METHOD'];
        }

        // test each route against the path
        foreach($aRoute as  $route) {

            // convert urls like '/users/:uid/posts/:pid' to regular expression
            $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route['url'])) . "$@D";

            $matches = Array();
            // check if the current request matches the expression
            if($requestMethod == $route['method'] && preg_match($pattern, $path, $matches)) {
                // remove the first match
                array_shift($matches);

                $callback = $route['callback'];

                try {

                    // handle string callback
                    if(is_string($callback)) {

                        // class::method
                        if(strpos($callback, '::') === false) {
                            throw new \Exception('callback requires class::method');
                        }

                        $aCallback = explode('::', $callback);

                        $class = $aCallback[0];
                        $method = $aCallback[1];

                        // class has to be callable
                        if(! class_exists($class)) {
                            throw new \Exception("invalid callback: class {$class} doesn't exist");
                        }

                        $object = new $class();

                        if(! method_exists($object, $method)) {
                            throw new \Exception("invalid callback: method {$method} doesn't exist in class {$class}");
                        }

                        $callback = [$object, $method];

                    }
                    elseif(! $this->validCallback($callback)) {
                        throw new \Exception("invalid closure for {$route['url']} -> {$callback}");
                    }

                    // call the callback with the matched positions as params
                    return call_user_func_array($callback, $matches);

                }
                catch(\Exception $e) {
                    $api = new ApiController();
                    $api->handleError($e);
                }
            }
        }

        if($path) {
            throw new ApiException("invalid method/path: {$requestMethod}/{$path}", 404); // NOT FOUND
        }

        return $response;
    }

    /**
     * @param $callback
     * @return bool
     */
    public function validCallback($callback)
    {
        return (is_string($callback) && function_exists($callback)) || (is_object($callback) && ($callback instanceof Closure));
    }
}