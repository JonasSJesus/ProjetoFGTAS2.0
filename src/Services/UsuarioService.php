<?php

namespace Fgtas\Services;

use Exception;
use Fgtas\Entities\Usuario;
use Fgtas\Repositories\Interfaces\IUsuarioRepository;

class UsuarioService
{
    private IUsuarioRepository $repository;

    /**
     * @param IUsuarioRepository $repository
     */
    public function __construct(IUsuarioRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * Cria usuarios no banco
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function registerUser(array $data): void
    {
        if ($this->repository->findByEmail($data['emailUsuario'])) {
            echo "Email ja cadastrado!";
            exit();
        }
        $passwordHashed = $this->hashPWD($data['senhaUsuario']);
        $user = new Usuario(
            $data['nomeUsuario'],
            $data['emailUsuario'],
            $data['cargo']
        );
        $user->setSenha($passwordHashed);

        $this->repository->create($user);
    }


    /**
     * Retorna um usuario com base no ID, ou todos os usuarios sem nenhum ID for passado
     * @param array|null $args
     * @return array|Usuario
     */
    public function getUser(array $args = null): array|Usuario
    {
        if (isset($args)) {
            return $this->repository->findById($args['id']);
        }
        return $this->repository->findAll();
    }


    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function update(array $data, int $id): bool
    {
        if (!$this->repository->findById($id)) {
            return false; // Melhor tratado com Exception talvez?
        }

//        $passwordHashed = $this->hashPWD($data['password']);
        $user = new Usuario(
            $data['nomeUsuario'],
            $data['emailUsuario'],
            $data['cargoUsuario']
        );

        $this->repository->update($user, $id);

        return true;
    }


    public function delete(int $id): bool
    {
        if (!$this->repository->findById($id)) {
            return false; // Melhor se tratado com Exception?
        }

        return $this->repository->delete($id);
    }


    // TODO -> apagar este metodo
    // deve-se verificar a existencia e o formato dos dados
//    private function validateData(array $data, ?int $id = null): array|null
//    {
//        $name = filter_var($data['nomeUsuario']);
//        $email = filter_var($data['emailUsuario'], FILTER_VALIDATE_EMAIL);
//        $role = filter_var($data['cargo']);
//        $password = strlen($data['senhaUsuario']) >= 5 ? $data['senhaUsuario'] : null;
//
//        if (!filter_var($id, FILTER_VALIDATE_INT)) {
//            throw new Exception('Usuario não encontrado');
//        }
//
//        if (empty($name) || empty($role)) {
//            throw new Exception('nome e cargo não foram inseridos');
//        }
//
//        if (!$email) {
//            throw new Exception('E-mail inválido. Por favor, insira um e-mail válido.');
//        }
//
//        if ($password === null) {
//            throw new Exception('Senha muito curta, escreva uma senha com 5 caracteres ou mais');
//        }
//
//        return [
//            'name' => $name,
//            'email' => $email,
//            'role' => $role,
//            'password' => $password,
//            'id' => $id
//        ];
//    }


    /**
     * @param string $password
     * @return string
     */
    private function hashPWD(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
}
