<?php

namespace Fgtas\Repositories\Interfaces;

use Fgtas\Entities\Publico;

interface IPublicoRepository
{
    public function add(Publico $publico): int;

//    private function insertExtraFields(Publico $publico, int $publicoId): void;

    /** @return null|Publico[] */
    public function findAll(): ?array;

    public function findById(int $id): ?Publico;

    public function update(Publico $publico, int $id): bool;

    public function delete(int $id): bool;
}