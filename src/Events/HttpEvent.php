<?php

namespace Trochilidae\Events;

use Psr\Http\Server\RequestHandlerInterface;
use Trochilidae\Http\Request;
use Swoole\Http\Request as SwooleRequist;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Http\Server;
use Trochilidae\Http\Response;
use Trochilidae\Http\URI;

class HttpEvent
{

    public function request(SwooleRequist $request, SwooleResponse $response)
    {
        /**
         * @var $psrResponse Response
         */
        $psrRequest = $this->transformRequest($request);
        $psrResponse = app()[RequestHandlerInterface::class]->handle($psrRequest);

        $response->status($psrResponse->getStatusCode());
        $response->end($psrResponse->getBody()->getContents());
    }


    public function onStart(Server $server)
    {
        /**
         * 注册异常捕捉
         * 注册之前的报出的异常不能被捕捉
         */
        //App::get('exception')->register();

        // 检查目录、注册集群等
    }

    public function onShutdown(Server $server)
    {
    }


    public function onWorkerStart(Server $server)
    {

    }


    public function onWorkerStop(Server $server, int $workerId)
    {
    }


    public function onConnect(Server $server, int $fd, int $reactorId)
    {

    }


    public function onClose(Server $server, int $fd, int $reactorId)
    {
    }


    public function onTask(Server $server, int $taskId, int $srcWorkerId, array $data)
    {

    }


    public function onFinish(Server $server, int $taskId, $data)
    {
        //return $data;
    }

    private function transformRequest(SwooleRequist $request): Request
    {
        $psrRequest = new Request();
        $psrRequest->withMethod($request->server['request_method']);

        $uri = new URI();
        $uri->withPath($request->server['request_uri']);
        if (isset($request->server['query_string'])) {
            $uri->withQuery($request->server['query_string']);
        }

        $psrRequest->withUri($uri);
        return $psrRequest;
    }
}