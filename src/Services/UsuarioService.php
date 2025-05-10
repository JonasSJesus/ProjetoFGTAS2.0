<?php

namespace Fgtas\Services;

use Exception;
use Fgtas\Entities\Usuario;
use Fgtas\Exceptions\EmailAlreadyExistsException;
use Fgtas\Exceptions\UserNotFoundException;
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
     * @throws EmailAlreadyExistsException
     */
    public function registerUser(array $data): void
    {
        if ($this->repository->findByEmail($data['emailUsuario'])) {
            throw new EmailAlreadyExistsException();
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
     * Retorna um usuario com base no ID, ou todos os usuarios se nenhum ID for passado
     * @param int|null $id
     * @return array|Usuario
     * @throws UserNotFoundException
     */
    public function getUser(int $id = null): array|Usuario
    {
        if (isset($id)) {
            $user = $this->repository->findById($id);
            if (!$user) {
                throw new UserNotFoundException();
            } else {
                return $user;
            }
        }
        return $this->repository->findAll();
    }


    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function update(array $data, int $id): string
    {
        if (!$this->repository->findById($id)) {
            throw new UserNotFoundException();
        }

//        $passwordHashed = $this->hashPWD($data['password']);
        $user = new Usuario(
            $data['nomeUsuario'],
            $data['emailUsuario'],
            $data['cargoUsuario']
        );

        $this->repository->update($user, $id);

        return "Usuário atualizado com sucesso";
    }


    /**
     * @param int $id
     * @return bool
     * @throws UserNotFoundException
     */
    public function delete(int $id): bool
    {
        if (!$this->repository->findById($id)) {
            throw new UserNotFoundException();
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
