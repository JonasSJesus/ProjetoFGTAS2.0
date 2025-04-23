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
            echo "Email ja cadastrado!"; // TODO: Flash Messages
            return;
        }
        $passwordHashed = $this->hashPWD($data['senhaUsuario']);

        $user = new Usuario(
            $data['nomeUsuario'],
            $data['emailUsuario'],
            $data['cargo']
        );
        $user->setSenha($passwordHashed);

        $this->repository->create($user); // TODO: Trocar o nome do metodo no UsuarioRepository
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


    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        if (!$this->repository->findById($id)) {
            return false; // Melhor se tratado com Exception? | Devolver erro 404 ou Flash Message.
        }

        return $this->repository->delete($id);
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
