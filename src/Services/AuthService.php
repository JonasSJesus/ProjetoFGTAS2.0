<?php

namespace Fgtas\Services;

use Exception;
use Fgtas\Entities\Usuario;
use Fgtas\Repositories\UsuarioRepository;
use SlimSession\Helper;

class AuthService
{
    private UsuarioRepository $repository;
    private Helper $session;

    public function __construct(UsuarioRepository $repository, Helper $session)
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
    public function authenticate(string $email, string $password) : bool
    {
        $user = $this->repository->getBy(UsuarioRepository::EMAIL, $email);
        $dbPassword = password_hash(' ', PASSWORD_ARGON2ID);

        if ($user) {
            $dbPassword = $user->senha;
        }

        $correctPWD = password_verify($password, $dbPassword);

        if (!$correctPWD){
            return false;
        }

        $this->createSession($user);


        if(password_needs_rehash($user->senha, PASSWORD_ARGON2ID)){
            $hashPWD = password_hash($password, PASSWORD_ARGON2ID);
            $this->repository->updatePWD($user->id, $hashPWD);
        }

        return true;
    }

    private function createSession(Usuario $user): void
    {
        $this->session->set('id', $user->id);
        $this->session->set('name', $user->nome);
        $this->session->set('email', $user->email);
        $this->session->set('role', $user->cargo);
        $this->session->set('is_logged', true);
    }

    public function destroySession(): void
    {
        $this->session::destroy();
    }
}