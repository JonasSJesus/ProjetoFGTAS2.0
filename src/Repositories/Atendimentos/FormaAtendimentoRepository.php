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