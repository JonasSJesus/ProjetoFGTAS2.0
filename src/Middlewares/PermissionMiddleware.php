<?php

namespace Fgtas\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use SlimSession\Helper;

class PermissionMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;
    private Helper $session;

    public function __construct(ResponseFactoryInterface $responseFactory, Helper $session)
    {
        $this->responseFactory = $responseFactory;
        $this->session = $session;
    }

    /**
     * Checa o cargo do usuario, se não for admin, barra a passagem
     *
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $session = $this->session;

        if (!isset($session->user['role']) || $session->user['role'] !== 'admin') {
            $response = $this->responseFactory->createResponse(302);

            return $response
                    ->withHeader('Location', '/home');
        }

        return $handler->handle($request);
    }
}