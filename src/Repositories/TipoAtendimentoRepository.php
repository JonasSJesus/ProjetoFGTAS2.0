<?php

namespace Fgtas\Repositories;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception;
use Fgtas\Database\Connection;
use Fgtas\Entities\TipoAtendimento;
use Fgtas\Repositories\Interfaces\ITipoAtendimentoRepository;

class TipoAtendimentoRepository implements ITipoAtendimentoRepository
{
    private DBALConnection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn->getConnection();
    }

    public function add(TipoAtendimento $tipoAtend): int
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->insert('tipo_atendimento')
            ->values([
                'tipo' => ':tipo',
                'descricao' => ':descricao'
            ])->setParameters([
                'tipo' => $tipoAtend->tipo,
                'descricao' => $tipoAtend->descricao
            ]);
            
        $queryBuilder->executeStatement();

        $id = (int) $this->conn->lastInsertId();
        //$tipoAtend->setId(intval($id));

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): ?array
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $resultSet = $queryBuilder
            ->select('*')
            ->from('tipo_atendimento')
            ->executeQuery();
        $data = $resultSet->fetchAllAssociative();

        return array_map(TipoAtendimento::fromArray(...), $data);
    }

    /**
     * @param int $id
     * @return TipoAtendimento|null
     * @throws Exception
     */
    public function findById(int $id): ?TipoAtendimento
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $resultSet = $queryBuilder
            ->select('*')
            ->from('tipo_atendimento')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $data = $resultSet->fetchAssociative();

        if (empty($data)) {
            return null;
        }

        return TipoAtendimento::fromArray($data);
    }

    public function update(TipoAtendimento $tipoAtend, int $id): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->update('tipo_atendimento')
            ->set('tipo', ':tipo')
            ->where('id = :id')
            ->setParameters([
                'tipo' => $tipoAtend->tipo,
                'id' => $id
            ]);

        return $queryBuilder->executeStatement();
    }

    public function delete(int $id): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();
        $queryBuilder
            ->delete('tipo_atendimento')
            ->where('id = :id')
            ->setParameter('id', $id);

        return $queryBuilder->executeStatement();
    }
}