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
    private Helper $session;

    public function __construct(ResponseFactoryInterface $responseFactory, Helper $session)
    {
        $this->responseFactory = $responseFactory;
        $this->session = $session;
    }


    // implementar métodos para checar se $_SESSION esta definido
    public function process(Request $request, RequestHandler $handler): Response
    {
        $session = $this->session;
        session_regenerate_id(true); // TODO trocar por um middleware dedicado

        if (!$session->exists('user') && $session->user['is_logged'] !== true) {
            $response = $this->responseFactory->createResponse();

            return $response
                    ->withHeader('Location', '/login')
                    ->withStatus(302);
        }

        return $handler->handle($request);
    }
}