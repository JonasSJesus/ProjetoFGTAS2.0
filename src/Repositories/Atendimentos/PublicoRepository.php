<?php

namespace Fgtas\Repositories\Atendimentos;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception as DBALException;
use Exception;
use Fgtas\Database\Connection;
use Fgtas\Entities\Publico;
use Fgtas\Exceptions\DatabaseException;
use Fgtas\Repositories\Interfaces\IPublicoRepository;

class PublicoRepository implements IPublicoRepository
{
    private DBALConnection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn->getConnection();
    }


    /**
     * @param Publico $publico
     * @return int
     * @throws Exception
     */
    public function add(Publico $publico): int
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->insert('publico')
            ->values([
                'perfil_cliente' => ':perfil_cliente'
            ])
            ->setParameter('perfil_cliente', $publico->perfilCliente);

        $queryBuilder->executeStatement();

        $id = $this->conn->lastInsertId();
        //$publico->setId(intval($id));

        if ($publico->haveExtraFields()) {
            $this->insertExtraFields($publico, $id);
        }

        return $id;
    }

    /**
     * @throws Exception|DBALException
     */
    private function insertExtraFields(Publico $publico, int $publicoId): void
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        if (!$publico->haveExtraFields()) {
            return;
        }

        $queryBuilder
            ->insert('informacoes_pessoais')
            ->values([
                'publico_id' => ':publico_id',
                'nome' => ':nome',
                'contato' => ':contato',
                'documento' => ':documento'
            ])
            ->setParameters([
                'publico_id' => $publicoId,
                'nome' => $publico->getExtraFields()->nome,
                'contato' => $publico->getExtraFields()->contato,
                'documento' => $publico->getExtraFields()->documento
            ]);


        $queryBuilder->executeStatement();
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function findAll(): ?array
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        try {
            $resultSet = $queryBuilder
                ->select('*')
                ->from('publico')
                ->executeQuery();
            $data = $resultSet->fetchAllAssociative();
        } catch (DBALException $e) {
            throw new Exception($e->getMessage());
        }

        return array_map(Publico::fromArray(...), $data);
    }

    public function findById(int $id): ?Publico
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $resultSet = $queryBuilder
            ->select('*')
            ->from('publico')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $data = $resultSet->fetchAssociative();

        if (empty($data)) {
            return null;
        }

        return Publico::fromArray($data);
    }


    /**
     * @param string $perfilCliente
     * @return int Retorna o ID do perfil de publico previamente cadastrado ou false se nenhum valor for encotrado.
     * @throws DatabaseException
     */
    public function findIdByName(string $perfilCliente): int
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        // TODO: Tirar o catch, talvez...
        try {
            $resultSet = $queryBuilder
                ->select('*')
                ->from('publico')
                ->where('perfil_cliente = :perfil_cliente')
                ->setParameter('perfil_cliente', $perfilCliente)
                ->executeQuery();

            $data = $resultSet->fetchAssociative();

            return $data['id'];
        } catch (DBALException $e) {
            throw new DatabaseException();
        }

    }

    public function update(Publico $publico, int $id): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->update('publico')
            ->set('perfil_cliente', ':perfil_cliente')
            ->where('id = :id')
            ->setParameters([
                'perfil_cliente' => $publico->perfilCliente,
                'id' => $id
            ]);

        return $queryBuilder->executeStatement();
    }

    public function delete(int $id): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();
        $queryBuilder
            ->delete('publico')
            ->where('id = :id')
            ->setParameter('id', $id);
        
        return $queryBuilder->executeStatement();
    }
}