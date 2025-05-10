<?php

namespace Fgtas\Exceptions;

use DomainException;

class EmailAlreadyExistsException extends DomainException
{

    public function __construct()
    {
        parent::__construct("E-mail selecionado já está em uso, tente novamente");
    }
}