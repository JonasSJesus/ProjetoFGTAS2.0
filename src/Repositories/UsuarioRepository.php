<?php

namespace Fgtas\Repositories;

use Fgtas\Entities\Usuario;
use PDO;
use PDOException;

class UsuarioRepository
{
    // Filtros usados no metodo getBy
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * Retorna todos os usuarios cadastrados no banco
     *
     * @return Usuario[]
     */
    public function getAll(): array
    {
        try {
            $sql = "SELECT * FROM usuario";
            $stmt = $this->pdo->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e;
        }

        return array_map($this->createObj(...), $data);
    }

    /**
     * Busca um usuario com base no id
     * @param int $id
     * @return Usuario|null
     */
    public function getById(int $id): ?Usuario
    {

        $sql = "SELECT * FROM usuario WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->createObj($data);
    }

    /**
     * Busca um usuario com base no e-mail
     * @param string $email
     * @return Usuario|null
     */
    public function getByEmail(string $email): ?Usuario
    {

        $sql = "SELECT * FROM usuario WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->createObj($data);
    }

    /**
     * insere um novo usuario no banco de dados
     *
     * @param Usuario $user o Usuario que queremos salvar no banco
     * @return bool indica se a operacao foi bem ou mal sucedida
     */
    public function create(Usuario $user): bool
    {

        $sql = "INSERT INTO usuario (nome, email, senha, cargo, ativo) VALUES (:nome, :email, :senha, :cargo, :ativo);";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $user->nome);
        $stmt->bindValue(':email', $user->email);
        $stmt->bindValue(':senha', $user->getSenha());
        $stmt->bindValue(':cargo', $user->cargo);
        $stmt->bindValue(':ativo', $user->ativo);

        return $stmt->execute();

    }

    public function update(Usuario $user): bool
    {
        $sql = "UPDATE usuario SET nome = :nome, email = :email, cargo = :cargo, ativo = :ativo WHERE id = :id;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $user->nome);
        $stmt->bindValue(':email', $user->email);
        $stmt->bindValue(':cargo', $user->cargo);
        $stmt->bindValue(':ativo', $user->ativo);
        $stmt->bindValue(':id', $user->getId());

        return $stmt->execute();
    }

    public function updatePWD(int $id, string $password)
    {
        $sql = "UPDATE usuario SET senha = :password WHERE id = :id;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }


    public function delete(int $id): bool
    {
        $sql = "DELETE FROM usuario WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }


    /**
     * Metodo interno. Usado para criar usuarios a partir dos dados do banco de dados
     * @param array $data
     * @return Usuario
     */
    private function createObj(array $data): Usuario
    {
        $user = new Usuario($data['nome'], $data['email'], $data['cargo'], $data['ativo']);
        $user->setSenha($data['senha']);
        $user->setId($data['id']);

        return $user;
    }
}