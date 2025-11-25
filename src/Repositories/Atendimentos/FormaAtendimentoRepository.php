<?php

namespace Fgtas\Repositories\Atendimentos;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception as DBALException;
use Fgtas\Database\Connection;
use Fgtas\Entities\FormaAtendimento;
use Fgtas\Exceptions\DatabaseException;
use Fgtas\Repositories\Interfaces\IFormaAtendimentoRepository;

class FormaAtendimentoRepository implements IFormaAtendimentoRepository
{
    private DBALConnection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn->getConnection();
    }

    public function add(FormaAtendimento $formaAtend): int
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->insert('forma_atendimento')
            ->values([
                'forma' => ':forma']
            )->setParameter('forma', $formaAtend->forma);

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
        $data = $queryBuilder
            ->select('*')
            ->from('forma_atendimento')
            ->executeQuery()
            ->fetchAllAssociative();
        dd($data);

        return array_map(FormaAtendimento::fromArray(...), $data);
    }

    public function findById(int $id): ?FormaAtendimento
    {
        $queryBuilder = $this->conn->createQueryBuilder();
        
        $data = $queryBuilder
            ->select('*')
            ->from('forma_atendimento')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        if (empty($data)) {
            return null;
        }

        return FormaAtendimento::fromArray($data);
    }

    /**
     * @param string $formaAtendimento
     * @return int Retorna o ID do perfil de publico previamente cadastrado ou false se nenhum valor for encotrado.
     * @throws DatabaseException
     */
    public function findIdByName(string $formaAtendimento): int
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        // TODO: Tirar o catch, talvez...
        try {
            $resultSet = $queryBuilder
                ->select('*')
                ->from('forma_atendimento')
                ->where('forma = :forma')
                ->setParameter('forma', $formaAtendimento)
                ->executeQuery();

            $data = $resultSet->fetchAssociative();

            return $data['id'];
        } catch (DBALException $e) {
            throw new DatabaseException();
        }

    }

    public function update(FormaAtendimento $formAtend, int $id): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();
        
        $affectedRows = $queryBuilder
            ->update('forma_atendimento')
            ->set('forma', ':forma')
            ->where('id = :id')
            ->setParameter('forma', $formAtend->forma)
            ->setParameter('id', $id)
            ->executeStatement();

        return $affectedRows > 0;
    }

    public function delete(int $id): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();
        
        $affectedRows = $queryBuilder
            ->delete('forma_atendimento')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeStatement();

        return $affectedRows > 0;
    }
}