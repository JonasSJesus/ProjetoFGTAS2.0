<?php

namespace Fgtas\Exceptions;

use DomainException;
use Exception;

class UserNotFoundException extends Exception
{

    public function __construct()
    {
        parent::__construct("Usuário não encontrado");
    }
}