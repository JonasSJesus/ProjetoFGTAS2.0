<?php

namespace Fgtas\Repositories\Interfaces;


use Fgtas\Entities\Atendimento;

interface IAtendimentoRepository
{
//    public function add(Atendimento $atendimento, int $idUsuario): bool;
    public function add(int $idTipoAtend, int $idUsuario, int $idPublico, int $idForma): bool;

    /** @return null|Atendimento[] */
    public function findAll(array $filters): ?array;

    public function findById(int $id): ?Atendimento;

    public function findForeignKeys(int $id): array|null;

//    public function update(Atendimento $atendimento, int $id): bool;
    public function update(int $idTipo, int $idPublico, int $idForma, int $idAtendimento): bool;

    public function delete(int $id): bool;
}