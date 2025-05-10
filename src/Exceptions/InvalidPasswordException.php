<?php

namespace Fgtas\Exceptions;

use DomainException;

class InvalidPasswordException extends DomainException
{

    public function __construct()
    {
        parent::__construct("E-Mail ou Senha incorretos, Tente novamente");
    }
}