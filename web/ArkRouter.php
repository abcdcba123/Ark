<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/2/13
 * Time: 17:58
 */

namespace sinri\ark\web;


use sinri\ark\core\ArkLogger;
use sinri\ark\io\ArkWebInput;

class ArkRouter
{
    protected $debug;
    protected $logger;
    protected $defaultControllerName = null;
    protected $defaultMethodName = null;
    protected $errorHandler = null;

    /**
     * @var ArkRouterRule[]
     */
    protected $routes;

    public function __construct()
    {
        $this->debug = false;
        $this->logger = ArkLogger::makeSilentLogger();
        $this->defaultControllerName = 'Welcome';
        $this->defaultMethodName = 'index';
        $this->errorHandler = null;
        $this->routes = [];
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param ArkLogger $logger
     */
    public function setLogger(ArkLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param null|string $defaultControllerName
     */
    public function setDefaultControllerName(string $defaultControllerName)
    {
        $this->defaultControllerName = $defaultControllerName;
    }

    /**
     * @param null|string $defaultMethodName
     */
    public function setDefaultMethodName(string $defaultMethodName)
    {
        $this->defaultMethodName = $defaultMethodName;
    }

    /**
     * Give a string as template file path for display-page use;
     * give an anonymous function or a callable definition array which consume one parameter of array,
     * or leave it as null to response JSON.
     * @param ArkRouteErrorHandler|null $errorHandler
     */
    public function setErrorHandler($errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @param array $errorData
     * @param int $httpCode @since 1.2.8
     */
    public function handleRouteError($errorData = [], $httpCode = 404)
    {
        if (!$this->errorHandler) {
            $this->errorHandler = new ArkRouteErrorHandler();
        }
        $this->errorHandler->execute($errorData, $httpCode);
    }

    /**
     * @param ArkRouterRule $routeRule
     */
    public function registerRouteRule($routeRule)
    {
        array_unshift($this->routes, $routeRule);
    }

    /**
     * @param string $path `posts/{post}/comments/{comment}` no leading `/`
     * @param callable $callback a function with parameters in path, such as `function($post,$comment)` for above
     * @param ArkRequestFilter[] $filters ArkRequestFilter
     */
    public function get($path, $callback, $filters = [])
    {
        $route_rule = ArkRouterRule::buildRouteRule(ArkWebInput::METHOD_GET, $path, $callback, $filters);
        $this->registerRouteRule($route_rule);
    }

    /**
     * @param string $path `posts/{post}/comments/{comment}` no leading `/`
     * @param callable $callback a function with parameters in path, such as `function($post,$comment)` for above
     * @param ArkRequestFilter[] $filters ArkRequestFilter
     */
    public function post($path, $callback, $filters = [])
    {
        $route_rule = ArkRouterRule::buildRouteRule(ArkWebInput::METHOD_POST, $path, $callback, $filters);
        $this->registerRouteRule($route_rule);
    }

    /**
     * @param string $path `posts/{post}/comments/{comment}` no leading `/`
     * @param callable $callback a function with parameters in path, such as `function($post,$comment)` for above
     * @param ArkRequestFilter[] $filters ArkRequestFilter
     */
    public function put($path, $callback, $filters = [])
    {
        $route_rule = ArkRouterRule::buildRouteRule(ArkWebInput::METHOD_PUT, $path, $callback, $filters);
        $this->registerRouteRule($route_rule);
    }

    /**
     * @param string $path `posts/{post}/comments/{comment}` no leading `/`
     * @param callable $callback a function with parameters in path, such as `function($post,$comment)` for above
     * @param ArkRequestFilter[] $filters ArkRequestFilter
     */
    public function patch($path, $callback, $filters = [])
    {
        $route_rule = ArkRouterRule::buildRouteRule(ArkWebInput::METHOD_PATCH, $path, $callback, $filters);
        $this->registerRouteRule($route_rule);
    }

    /**
     * @param string $path `posts/{post}/comments/{comment}` no leading `/`
     * @param callable $callback a function with parameters in path, such as `function($post,$comment)` for above
     * @param ArkRequestFilter[] $filters ArkRequestFilter
     */
    public function delete($path, $callback, $filters = [])
    {
        $route_rule = ArkRouterRule::buildRouteRule(ArkWebInput::METHOD_DELETE, $path, $callback, $filters);
        $this->registerRouteRule($route_rule);
    }

    /**
     * @param string $path `posts/{post}/comments/{comment}` no leading `/`
     * @param callable $callback a function with parameters in path, such as `function($post,$comment)` for above
     * @param ArkRequestFilter[] $filters ArkRequestFilter
     */
    public function option($path, $callback, $filters = [])
    {
        $route_rule = ArkRouterRule::buildRouteRule(ArkWebInput::METHOD_OPTION, $path, $callback, $filters);
        $this->registerRouteRule($route_rule);
    }

    /**
     * @param string $path `posts/{post}/comments/{comment}` no leading `/`
     * @param callable $callback a function with parameters in path, such as `function($post,$comment)` for above
     * @param ArkRequestFilter[] $filters ArkRequestFilter
     */
    public function head($path, $callback, $filters = [])
    {
        $route_rule = ArkRouterRule::buildRouteRule(ArkWebInput::METHOD_HEAD, $path, $callback, $filters);
        $this->registerRouteRule($route_rule);
    }

    /**
     * @param string $path `posts/{post}/comments/{comment}` no leading `/`
     * @param callable $callback a function with parameters in path, such as `function($post,$comment)` for above
     * @param ArkRequestFilter[] $filters ArkRequestFilter
     */
    public function any($path, $callback, $filters = [])
    {
        $route_rule = ArkRouterRule::buildRouteRule(ArkWebInput::METHOD_ANY, $path, $callback, $filters);
        $this->registerRouteRule($route_rule);
    }

    /**
     * @param $path
     * @param $method
     * @return ArkRouterRule
     * @throws \Exception
     */
    public function seekRoute($path, $method)
    {
        // a possible fix in 2.1.4
        if (strlen($path) > 1 && substr($path, strlen($path) - 1, 1) == '/') {
            $path = substr($path, 0, strlen($path) - 1);
        } elseif ($path == '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            $route_regex = $route->getPath();//$route[self::ROUTE_PARAM_PATH];
            $route_method = $route->getMethod();//$route[self::ROUTE_PARAM_METHOD];
            if ($this->debug) {
                $this->logger->debug(__METHOD__ . " TRY TO MATCH RULE: [$route_method][$route_regex][$path]");
            }
            if (
                $route_method !== ArkWebInput::METHOD_ANY
                && stripos($route_method, $method) === false
            ) {
                if ($this->debug) {
                    $this->logger->debug(__METHOD__ . " ROUTE METHOD NOT MATCH [$method]");
                }
                continue;
            }
            if (preg_match($route_regex, $path, $matches)) {
                if (!empty($matches)) array_shift($matches);
                $matches = array_filter($matches, function ($v) {
                    return substr($v, 0, 1) != '/';
                });
                $matches = array_values($matches);
                array_walk($matches, function (&$v) {
                    $v = urldecode($v);
                });
                $route->setParsed($matches);
                if ($this->debug) {
                    $this->logger->debug(__METHOD__ . " MATCHED with " . json_encode($matches));
                }
                return $route;
            }
        }
        throw new \Exception("No route matched: path={$path} method={$method}");

    }

    /**
     * @param ArkRouterRule $shared
     * @param ArkRouterRule[] $list
     */
    public function group($shared, $list)
    {
        $filters = null;
        $sharedPath = '';
        $sharedNamespace = '';

        if ($shared->getFilters() !== null) {
            $filters = $shared->getFilters();
        }
        if ($shared->getPath() !== null) {
            $sharedPath = $shared->getPath();
        }
        if ($shared->getNamespace() !== null) {
            $sharedNamespace = $shared->getNamespace();
        }

        foreach ($list as $item) {
            $callback = $item->getCallback();
            if (is_array($callback) && isset($callback[0]) && is_string($callback[0])) {
                $callback[0] = $sharedNamespace . $callback[0];
            }
            $route_rule = ArkRouterRule::buildRouteRule(
                $item->getMethod(),//$item[self::ROUTE_PARAM_METHOD],
                $sharedPath . $item->getPath(),//$item[self::ROUTE_PARAM_PATH],
                $callback,
                $filters
            );
            $this->registerRouteRule($route_rule);
        }
    }

    /**
     * @param string $directory __DIR__ . '/../controller'
     * @param string $urlBase "XX/"
     * @param string $controllerNamespaceBase '\sinri\sample\controller\\'
     * @param string $filters '\sinri\sample\filter\AuthFilter'
     */
    public function loadAllControllersInDirectoryAsCI($directory, $urlBase = '', $controllerNamespaceBase = '', $filters = '')
    {
        if (!file_exists($directory) || !is_dir($directory)) {
            if ($this->debug) {
                $this->logger->debug(__METHOD__ . " warning: this is not a directory: " . $directory);
            }
            return;
        }
        if ($handle = opendir($directory)) {
            if (
                $this->defaultControllerName
                && file_exists($directory . '/' . $this->defaultControllerName . '.php')
                && $this->defaultMethodName
                && method_exists($controllerNamespaceBase . $this->defaultControllerName, $this->defaultMethodName)
            ) {
                $this->any(
                    $urlBase . '?',
                    [$controllerNamespaceBase . $this->defaultControllerName, $this->defaultMethodName],
                    $filters
                );
            }
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    if (is_dir($directory . '/' . $entry)) {
                        //DIR,
                        $this->loadAllControllersInDirectoryAsCI(
                            $urlBase . $entry . '/',
                            $controllerNamespaceBase . $entry . '\\',
                            $filters
                        );
                    } else {
                        //FILE
                        $list = explode('.', $entry);
                        $name = isset($list[0]) ? $list[0] : '';
                        if (
                            $this->defaultMethodName
                            && method_exists($controllerNamespaceBase . $name, $this->defaultMethodName)
                        ) {
                            $this->any(
                                $urlBase . $name . '/?',
                                [$controllerNamespaceBase . $name, $this->defaultMethodName],
                                $filters
                            );
                        }
                        $this->loadController(
                            $urlBase . $name . '/',
                            $controllerNamespaceBase . $name,
                            $filters
                        );
                    }
                }
            }
            closedir($handle);
        }
    }

    /**
     * @param string $basePath "" or "xx/"
     * @param string $controllerClass full class describer
     * @param ArkRequestFilter[]|null $filters
     */
    public function loadController($basePath, $controllerClass, $filters = null)
    {
        try {
            $method_list = get_class_methods($controllerClass);
            $reflector = new \ReflectionClass($controllerClass);
            foreach ($method_list as $method) {
                if (strpos($method, '_') === 0) {
                    continue;
                }
                $path = $basePath . $method;
                $parameters = $reflector->getMethod($method)->getParameters();
                $after_string = "";
                $came_in_default_area = false;
                if (!empty($parameters)) {
                    foreach ($parameters as $param) {
                        if ($param->isDefaultValueAvailable()) {
                            $path .= "(";
                            $after_string .= ")?";
                            $came_in_default_area = true;
                        } elseif ($came_in_default_area) {
                            //non-default after default
                            if ($this->debug) {
                                $this->logger->debug("ROUTE SETTING ERROR: required-parameter after non-required-parameter");
                            }
                            return;
                        }
                        $path .= '/{' . $param->name . '}';
                    }
                    $path .= $after_string;
                }
                $route_rule = ArkRouterRule::buildRouteRule(ArkWebInput::METHOD_ANY, $path, [$controllerClass, $method], $filters);
                $this->registerRouteRule($route_rule);
                if ($method == $this->defaultMethodName) {
                    $basePathX = $basePath;
                    if (strlen($basePathX) > 0) {
                        $basePathX = substr($basePathX, 0, strlen($basePathX) - 1);
                    }
                    $route_rule = ArkRouterRule::buildRouteRule(ArkWebInput::METHOD_ANY, $basePathX, [$controllerClass, $method], $filters);
                    $this->registerRouteRule($route_rule);
                }
            }
        } catch (\Exception $exception) {
            // do nothing if class not exist
        }
    }
}