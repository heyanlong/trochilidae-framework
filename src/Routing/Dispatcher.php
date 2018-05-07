<?php
/**
 * Created by PhpStorm.
 * User: heyanlong
 * Date: 2018/5/4
 * Time: 下午3:04
 */

namespace Trochilidae\Routing;

use FastRoute\RouteCollector;
use Psr\Http\Server\RequestHandlerInterface;
use Trochilidae\Contracts\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Trochilidae\Http\Response;
use function FastRoute\simpleDispatcher;
use Trochilidae\Http\Stream;

class Dispatcher implements RequestHandlerInterface
{
    protected $dispatcher;

    public function __construct()
    {
        $this->dispatcher = simpleDispatcher(function (RouteCollector $route) {
            try {
                Route::setRoute($route);
                include app()->get('path.base') . '/routes/cms.php';
            } catch (\Exception $e) {

            }
        });
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $uri = (string)$request->getUri();

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $httpCode = Response::HTTP_OK;
        $s = fopen('php://temp', 'w+');
        $stream = new Stream($s);

        $routeInfo = $this->dispatcher->dispatch($method, $uri);

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND;
                $httpCode = Response::HTTP_NOT_FOUND;
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED;
                break;
            case \FastRoute\Dispatcher::FOUND;
                $stream->write($this->dispatch($request, $routeInfo));
                break;
        }

        $response = new Response();
        $response->withStatus($httpCode);
        $response->withBody($stream);
        return $response;
    }

    private function dispatch(ServerRequestInterface $request, $routeInfo): string
    {
        $namespace = $routeInfo[1]['namespace'];
        $controller = $routeInfo[1]['controller'];
        $action = $routeInfo[1]['action'];
        $controller = $namespace . $controller;

        $reflectionClass = new \ReflectionClass($controller);
        $reflectionMethod = $reflectionClass->getMethod($action);

        $params = [];

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $type = $parameter->getType()->getName();

            if ($type === Request::class) {
                $params[] = $request;
            }

        }


        return call_user_func_array([new $controller, $action], $params);
    }
}