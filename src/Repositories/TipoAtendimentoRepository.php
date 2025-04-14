<?php

namespace Fgtas\Repositories;

use Fgtas\Entities\TipoAtendimento;
use Fgtas\Repositories\Interfaces\ITipoAtendimentoRepository;
use PDO;

class TipoAtendimentoRepository implements ITipoAtendimentoRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(TipoAtendimento $tipoAtend): int
    {
        $sql = "INSERT INTO tipo_atendimento (tipo, descricao) VALUES (:tipo, :descricao)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':tipo', $tipoAtend->tipo);
        $stmt->bindValue(':descricao', $tipoAtend->descricao);
        $stmt->execute();

        $id = $this->pdo->lastInsertId();
        $tipoAtend->setId(intval($id));

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): ?array
    {
        $sql = "SELECT * FROM tipo_atendimento";
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(TipoAtendimento::fromArray(...), $data);
    }

    public function findById(int $id): ?TipoAtendimento
    {
        $sql = "SELECT * FROM tipo_atendimento WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($data)) {
            return null;
        }

        return TipoAtendimento::fromArray($data);
    }

    public function update(TipoAtendimento $tipoAtend, int $id): bool
    {
        $sql = "UPDATE tipo_atendimento SET tipo = :tipo WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':tipo', $tipoAtend->tipo);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM tipo_atendimento WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }
}