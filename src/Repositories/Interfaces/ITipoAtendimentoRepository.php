<?php

namespace Fgtas\Repositories\Interfaces;


use Fgtas\Entities\TipoAtendimento;

interface ITipoAtendimentoRepository
{
    public function create(TipoAtendimento $tipoAtend): bool;

    /** @return null|TipoAtendimento[] */
    public function findAll(): ?array;

    public function findById(int $id): ?TipoAtendimento;

    public function update(TipoAtendimento $tipoAtend, int $id): bool;

    public function delete(int $id): bool;
}