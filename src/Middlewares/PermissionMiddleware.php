<?php

namespace Fgtas\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class PermissionMiddleware
{
    // Implementar métodos para checar o cargo/role do usuario, e impedir acesso não autorizado
    public function process(Request $request, RequestHandler $handler): Response
    {
        return $handler->handle($request);
    }
}