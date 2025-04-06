<?php

namespace Fgtas\Repositories;

interface IRepository
{
    // metodos CRUD padrao
    public function create(array $data): bool;
    public function getAll(): array;

    public function getById(int $id): Object;


}