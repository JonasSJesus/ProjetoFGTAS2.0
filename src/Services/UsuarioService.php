<?php

namespace Fgtas\Services;

use Exception;
use Fgtas\Entities\Usuario;
use Fgtas\Repositories\UsuarioRepository;
use PDOException;

class UsuarioService
{
    private UsuarioRepository $repository;

    /**
     * @param UsuarioRepository $repository
     */
    public function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function create(array $data): void
    {
        $passwordHashed = $this->hashPWD($data['senhaUsuario']);
        $user = new Usuario(
            $data['nomeUsuario'],
            $data['emailUsuario'],
            $passwordHashed,
            $data['cargo']
        );

        $this->repository->create($user);
    }


    /**
     * Retorna Usuarios salvos no banco de dados. Um (com base nos filtros) ou todos (sem filtros)
     * @param ?array $args Um array contendo os filtros necessarios para buscar Usuarios no banco de dados. Se nao tiver nenhum filtro setado, retorna todos os Usuarios salvos.
     * @return array|Usuario
     */
    public function getUser(array $args = null): array|Usuario
    {
        if (isset($args)) {
            return $this->repository->getBy($args['column'], $args['filter']);
        }
        return $this->repository->getAll();
    }


    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function update(array $data, int $id): bool
    {
        if ($this->repository->getById($id)) {
            return false;
        }

        $passwordHashed = $this->hashPWD($data['password']);
        $user = new Usuario(
            $data['name'],
            $data['email'],
            $passwordHashed,
            $data['role']
        );
        $user->setId($data['id']);


        $this->repository->update($user);

        return true;
    }


    // TODO -> melhorar o metodo de validacao de dados
    // deve-se verificar a existencia e o formato dos dados
    /**
     * Metodo interno para validacao de dados vindos do formulario pelo controller
     * @param array $data
     * @return Usuario|null
     * @throws Exception
     */
    private function validateData(array $data, ?int $id = null): array|null
    {
        $name = filter_var($data['nomeUsuario']);
        $email = filter_var($data['emailUsuario'], FILTER_VALIDATE_EMAIL);
        $role = filter_var($data['cargo']);
        $password = strlen($data['senhaUsuario']) >= 5 ? $data['senhaUsuario'] : null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new Exception('Usuario não encontrado');
        }

        if (empty($name) || empty($role)) {
            throw new Exception('nome e cargo não foram inseridos');
        }

        if (!$email) {
            throw new Exception('E-mail inválido. Por favor, insira um e-mail válido.');
        }

        if ($password === null) {
            throw new Exception('Senha muito curta, escreva uma senha com 5 caracteres ou mais');
        }

        return [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => $password,
            'id' => $id
        ];
    }


    /**
     * @param string $password
     * @return string
     */
    private function hashPWD(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
}
