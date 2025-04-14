<?php

namespace Fgtas\Repositories\Interfaces;

use Fgtas\Entities\Publico;

interface IPublicoRepository
{
    public function create(Publico $publico): int;

    /** @return null|Publico[] */
    public function findAll(): ?array;

    public function findById(int $id): ?Publico;

    public function update(Publico $publico, int $id): bool;

    public function delete(int $id): bool;
}