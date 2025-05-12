<?php

namespace Fgtas\Exceptions;


use Exception;

class DatabaseException extends Exception
{
    public function __construct(string $message = "Erro Inesperado, entre em contato conosco através do email: suporte.atendimento@email.com", int $code = 500)
    {
        parent::__construct($message, $code);
    }
}