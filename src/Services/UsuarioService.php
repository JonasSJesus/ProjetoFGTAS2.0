<?php

namespace Fgtas\Services;

use Fgtas\Entities\Usuario;
use Fgtas\Repositories\UsuarioRepository;

class UsuarioService
{
    private UsuarioRepository $repository;

    /**
     * @param UsuarioRepository $repository
     */
    public function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }


    public function getAll()
    {
        $user = $this->repository->getAll();

        return $user;
    }
}