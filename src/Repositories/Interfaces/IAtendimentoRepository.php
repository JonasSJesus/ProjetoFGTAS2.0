<?php

namespace Fgtas\Repositories\Interfaces;


use Fgtas\Entities\Atendimento;

interface IAtendimentoRepository
{
    public function add(Atendimento $atendimento, int $idUsuario): bool;

    /** @return null|Atendimento[] */
    public function findAll(): ?array;

    public function findById(int $id): ?Atendimento;

    public function update(Atendimento $atendimento, int $id): bool;

    public function delete(int $id): bool;
}