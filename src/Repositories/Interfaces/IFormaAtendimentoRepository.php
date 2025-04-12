<?php

namespace Fgtas\Repositories\Interfaces;

use Fgtas\Entities\FormaAtendimento;

interface IFormaAtendimentoRepository
{
    public function create(FormaAtendimento $formAtend): bool;

    /** @return null|FormaAtendimento[] */
    public function findAll(): ?array;

    public function findById(int $id): ?FormaAtendimento;

    public function update(FormaAtendimento $formAtend, int $id): bool;

    public function delete(int $id): bool;
}