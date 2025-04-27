<?php

namespace Fgtas\Repositories\Atendimentos;

use Fgtas\Entities\FormaAtendimento;
use Fgtas\Repositories\Interfaces;
use PDO;

class FormaAtendimentoRepository implements Interfaces\IFormaAtendimentoRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(FormaAtendimento $formAtend): int
    {
        $sql = "INSERT INTO forma_atendimento (forma) VALUES (:forma)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':forma', $formAtend->forma);
        $stmt->execute();

        $id = $this->pdo->lastInsertId();
//        $formAtend->setId(intval($id));

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): ?array
    {
        $sql = "SELECT * FROM forma_atendimento";
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(FormaAtendimento::fromArray(...), $data);
    }

    public function findById(int $id): ?FormaAtendimento
    {
        $sql = "SELECT * FROM forma_atendimento WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($data)) {
            return null;
        }

        return FormaAtendimento::fromArray($data);
    }

    public function update(FormaAtendimento $formAtend, int $id): bool
    {
        $sql = "UPDATE forma_atendimento SET forma = :forma WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':forma', $formAtend->forma);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM forma_atendimento WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }
}