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
        $user = $this->validateData($data);

//        $this->repository->create($user);
        try { // Descomentar este codigo em producao! (ou quando o sistema de flash messages estiver implementado)
            $this->repository->create($user);
        } catch (PDOException $e) {
            throw new Exception("Database Error: " . $e->getMessage());
        }
    }


    /**
     * Retorna Usuarios salvos no banco de dados. Um (com base nos filtros) ou todos (sem filtros)
     * @param array $filters Um array contendo os filtros necessarios para buscar Usuarios no banco de dados. Se nao tiver nenhum filtro setado, retorna todos os Usuarios salvos.
     * @return array|Usuario
     */
    public function getUser(array $filters = null): array|Usuario
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
    public function update(array $data, int $id): void
    {
        try {
            $data = $this->validateData($data, $id);
            $passwordHashed = $this->hashPWD($data['password']);
            $user = new Usuario($data['name'], $data['email'], $passwordHashed, $data['role']);
            $user->setId($data['id']);

        } catch (Exception $e) {
            throw new Exception('Data Error: ' . $e->getMessage());
        }

        $this->repository->update($user);
    }


    // TODO -> melhorar o metodo de validacao de dados
    // deve-se verificar a existencia e o formato dos dados
    /**
     * Metodo interno para validacao de dados vindos do formulario pelo controller
     * @param array $data
     * @return Usuario|null
     * @throws Exception
     */
    private function validateData(array $data, int $id): array|null
    {
        $name = filter_var($data['nomeUsuario']);
        $email = filter_var($data['emailUsuario'], FILTER_VALIDATE_EMAIL);
        $role = filter_var($data['cargo']);
        $password = strlen($data['senhaUsuario']) >= 5 ? $data['senhaUsuario'] : null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new Exception('Usuario não encontrado');
        }

        if (empty($name) || empty($role)) {
            throw new Exception('Insira os dados necessários!');
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
