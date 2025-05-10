<?php

namespace Fgtas\Repositories\Usuario;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception;
use Fgtas\Database\Connection;
use Fgtas\Entities\Usuario;
use Fgtas\Exceptions\DatabaseException;
use Fgtas\Repositories\Interfaces\IUsuarioRepository;

class UsuarioRepository implements IUsuarioRepository
{
    // Filtros usados no metodo getBy
    private DBALConnection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn->getConnection();
    }


    /**
     * Retorna todos os usuarios cadastrados no banco
     *
     * @return Usuario[]
     * @throws Exception
     */
    public function findAll(): array
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $resultSet = $queryBuilder
            ->select('*')
            ->from('usuario')
            ->executeQuery();
        $data = $resultSet->fetchAllAssociative();

        return array_map($this->createObj(...), $data);
    }

    /**
     * Busca um usuario com base no id
     * @param int $id
     * @return Usuario|null
     * @throws Exception
     */
    public function findById(int $id): ?Usuario
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $resultSet = $queryBuilder
            ->select('*')
            ->from('usuario')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
        $data = $resultSet->fetchAssociative();

        if (!$data) {
            return null;
        }

        return $this->createObj($data);
    }

    /**
     * Busca um usuario com base no e-mail
     * @param string $email
     * @return Usuario|null
     * @throws DatabaseException
     */
    public function findByEmail(string $email): ?Usuario
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        try {
            $queryBuilder
                ->select('*')
                ->from('usuario')
                ->where('email = :email')
                ->setParameter('email', $email);

            $resultSet = $queryBuilder->executeQuery();
            $data = $resultSet->fetchAssociative();
        } catch (Exception $e) {
            throw new DatabaseException("Erro interno, contate um superior", 500);
        }

        if (!$data) {
            return null;
        }

//        return Usuario::fromArray($data); // Implementar isso depois!
        return $this->createObj($data);
    }

    /**
     * Insere um novo usuario no banco de dados
     *
     * @param Usuario $user o Usuario que queremos salvar no banco
     * @return bool indica se a operacao foi bem ou mal sucedida
     */
    public function create(Usuario $user): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->insert('usuario')
            ->values([
                'nome' => ':nome',
                'email' => ':email',
                'senha' => ':senha',
                'cargo' => ':cargo',
                'ativo' => ':ativo'
            ])->setParameters([
                'nome' => $user->nome,
                'email' => $user->email,
                'senha' => $user->getSenha(),
                'cargo' => $user->cargo,
                'ativo' => $user->ativo
            ]);

        return $queryBuilder->executeStatement();
    }

    /**
     * @param Usuario $user
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function update(Usuario $user, int $id): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->update('usuario')
            ->set('nome', ':nome')
            ->set('email', ':email')
            ->set('cargo', ':cargo')
            ->set('ativo', ':ativo')
            ->where('id = :id')
            ->setParameters([
                'nome' => $user->nome,
                'email' => $user->email,
                'cargo' => $user->cargo,
                'ativo' => $user->ativo,
                'id' => $id
            ]);

        return $queryBuilder->executeStatement();
    }

    /**
     * @param int $id
     * @param string $password
     * @return bool
     * @throws Exception
     */
    public function updatePWD(int $id, string $password): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->update('usuario')
            ->set('senha', ':password')
            ->where('id = :id')
            ->setParameters([
                'password' => $password,
                'id' => $id
            ]);

        return $queryBuilder->executeStatement();
    }

    /**
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->delete('usuario')
            ->where('id = :id')
            ->setParameter('id', $id);

        return $queryBuilder->executeStatement();
    }


    /**
     * Metodo interno. Usado para criar usuarios a partir dos dados do banco de dados
     * @param array $data
     * @return Usuario
     */
    private function createObj(array $data): Usuario
    {
        $user = new Usuario(
            $data['nome'],
            $data['email'],
            $data['cargo'],
            $data['ativo']
        );
        $user->setSenha($data['senha']);
        $user->setId($data['id']);

        return $user;
    }
}