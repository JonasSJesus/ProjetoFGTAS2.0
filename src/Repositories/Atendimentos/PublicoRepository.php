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
    public function add(Publico $publico, int|null $pessoaId): int
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->insert('publico')
            ->setValue('perfil_cliente', ':perfil_cliente')
            ->setParameter('perfil_cliente', $publico->perfilCliente);

        if ($pessoaId != null) {
            $queryBuilder
                ->setValue('pessoa_id', ':pessoa_id')
                ->setParameter('pessoa_id', $pessoaId);
        }

        $queryBuilder->executeStatement();

        //$publico->setId(intval($id));

        return $this->conn->lastInsertId();
    }

    /**
     * @throws Exception|DBALException
     */
    public function insertExtraFields(Publico $publico): int|false
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        if (!$publico->haveExtraFields()) {
            return false;
        }

        $queryBuilder
            ->insert('pessoa')
            ->values([
                'nome' => ':nome',
                'email' => ':email',
                'documento' => ':documento'
            ])
            ->setParameters([
                'nome' => $publico->getExtraFields()->nome,
                'email' => $publico->getExtraFields()->contato,
                'documento' => $publico->getExtraFields()->documento
            ]);

        $queryBuilder->executeStatement();

        return $this->conn->lastInsertId();
    }

    public function updateExtraFields(Publico $publico): int|false
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        if (!$publico->haveExtraFields()) {
            return false;
        }

        $queryBuilder
            ->update('pessoa')
            ->set('nome', ':nome')
            ->set('email', ':email')
            ->set('documento', ':documento')
            ->where('documento = :documento')
            ->setParameters([
                'nome' => $publico->getExtraFields()->nome,
                'email' => $publico->getExtraFields()->contato,
                'documento' => $publico->getExtraFields()->documento
            ]);

        $queryBuilder->executeStatement();

        return 123;
    }

    public function findIdByDocumento(string $documento): int|false
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $resultSet = $queryBuilder
            ->select('id')
            ->from('pessoa')
            ->where('documento = :documento')
            ->setParameter('documento', $documento)
            ->executeQuery();

        $data = $resultSet->fetchAssociative();

        if (empty($data)) {
            return false;
        }

        return $data['id'];
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

    public function update(Publico $publico, int $id, int|null $idPessoa): int
    {
        $queryBuilder = $this->conn->createQueryBuilder();


        $queryBuilder
            ->update('publico')
            ->set('perfil_cliente', ':perfil_cliente')
            ->set('pessoa_id', ':pessoa_id')
            ->where('id = :id')
            ->setParameters([
                'perfil_cliente' => $publico->perfilCliente,
                'id' => $id,
                'pessoa_id' => $idPessoa
            ])
            ->executeStatement();

        return $id;
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