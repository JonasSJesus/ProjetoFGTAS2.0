<?php

namespace Fgtas\Repositories\Interfaces;

use Fgtas\Entities\Usuario;

interface IUsuarioRepository
{
    public function create(Usuario $user): bool;

    public function update(Usuario $user, int $id): bool;

    public function updatePWD(int $id, string $password): bool;

    /** @return null|Usuario[] */
    public function findAll(): ?array;

    public function findById(int $id): ?Usuario;

    public function findByEmail(string $email): ?Usuario;

    public function delete(int $id): bool;
}