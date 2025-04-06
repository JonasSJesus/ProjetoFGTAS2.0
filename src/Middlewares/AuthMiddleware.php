<?php

namespace Fgtas\Middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use SlimSession\Helper;

class AuthMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }


    // implementar métodos para checar se $_SESSION esta definido
    public function process(Request $request, RequestHandler $handler): Response
    {
        $session = new Helper();

        if (!$session->exists('user')) {
            $response = $this->responseFactory->createResponse();

            return $response
                    ->withHeader('Location', '/login')
                    ->withStatus(301);
        }

        return $handler->handle($request);
    }
}