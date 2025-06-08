<?php

namespace Fgtas\Repositories\Interfaces;


use Fgtas\Entities\TipoAtendimento;

interface ITipoAtendimentoRepository
{
    public function add(TipoAtendimento $tipoAtend): int;

    /** @return null|TipoAtendimento[] */
    public function findAll(): ?array;

    public function findById(int $id): ?TipoAtendimento;

    public function update(TipoAtendimento $tipoAtend, int $id): int;

    public function delete(int $id): bool;
}