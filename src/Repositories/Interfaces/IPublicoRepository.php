<?php

namespace Fgtas\Repositories\Interfaces;

use Fgtas\Entities\Publico;

interface IPublicoRepository
{
    public function add(Publico $publico): int;

    public function insertExtraFields(Publico $publico, int $publicoId): bool;

    public function updateExtraFields(Publico $publico, int $id): bool;

    public function findIdByDocumento(string $documento): int|false;

    /** @return null|Publico[] */
    public function findAll(): ?array;

    public function findById(int $id): ?Publico;

    public function findIdByName(string $perfilCliente): int;

    public function update(Publico $publico, int $id): int;

    public function delete(int $id): bool;
}