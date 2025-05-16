<?php

namespace Fgtas\Repositories\Interfaces;

use Fgtas\Entities\FormaAtendimento;

interface IFormaAtendimentoRepository
{
    public function add(FormaAtendimento $formaAtend): int;

    /** @return null|FormaAtendimento[] */
    public function findAll(): ?array;

    public function findById(int $id): ?FormaAtendimento;

    public function findIdByName(string $forma): int;

    public function update(FormaAtendimento $formAtend, int $id): bool;

    public function delete(int $id): bool;
}