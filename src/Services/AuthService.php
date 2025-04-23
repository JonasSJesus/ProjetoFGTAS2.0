<?php

namespace Fgtas\Services;

use Fgtas\Entities\Usuario;
use Fgtas\Repositories\Interfaces\IUsuarioRepository;
use SlimSession\Helper;

class AuthService
{
    private IUsuarioRepository $repository;
    private Helper $session;

    public function __construct(IUsuarioRepository $repository, Helper $session)
    {
        $this->repository = $repository;
        $this->session = $session;
    }

    /**
     * Realiza a autenticação com base no email e senha.
     * Depois de autenticado, cria a sessão
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authenticate(string $email, string $password): bool
    {
        $user = $this->repository->findByEmail($email);
        $dbPassword = password_hash(' ', PASSWORD_ARGON2ID);

        if ($user) {
            $dbPassword = $user->getSenha();
        }

        $correctPWD = password_verify($password, $dbPassword);

        if (!$correctPWD){
            return false;
        }

        $this->createSession($user);

        if(password_needs_rehash($user->getSenha(), PASSWORD_ARGON2ID)){
            $hashPWD = password_hash($password, PASSWORD_ARGON2ID);
            $this->repository->updatePWD($user->getId(), $hashPWD);
        }

        return true;
    }

    private function createSession(Usuario $user): void
    {
        $dataUser = [
            'id' => $user->getId(),
            'name' => $user->nome,
            'email' => $user->email,
            'role' => $user->cargo,
            'is_logged' => true,
        ];
        $this->session->set('user', $dataUser);
    }

    public function destroySession(): void
    {
        $this->session::destroy();
    }

    private function verifySession(): bool
    {
        return $this->session->exists('user');
    }
}