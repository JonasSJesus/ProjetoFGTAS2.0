<?php

namespace Fgtas\Repositories;

use Fgtas\Entities\Publico;
use Fgtas\Repositories\Interfaces\IPublicoRepository;
use PDO;

class PublicoRepository implements IPublicoRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function create(Publico $publico): bool
    {
        $sql = "INSERT INTO publico (perfil_cliente) VALUES (:perfil_cliente)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':perfil_cliente', $publico->perfilCliente);

        return $stmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function findAll(): ?array
    {
        $sql = "SELECT * FROM publico";
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(Publico::fromArray(...), $data);
    }

    public function findById(int $id): ?Publico
    {
        $sql = "SELECT * FROM publico WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($data)) {
            return null;
        }

        return Publico::fromArray($data);
    }

    public function update(Publico $publico, int $id): bool
    {
        $sql = "UPDATE publico SET perfil_cliente = :perfil_cliente WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':perfil_cliente', $publico->perfilCliente);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM publico WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }
}