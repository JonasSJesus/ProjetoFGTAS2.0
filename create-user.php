<?php


use Dotenv\Dotenv;
use Fgtas\Database\Connection;
use Fgtas\Entities\Usuario;
use Fgtas\Repositories\Usuario\UsuarioRepository;

require_once "vendor/autoload.php";
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * ======================================+
 *                                       |
 * Script para criar o usuario admin     |
 * Altere a senha da forma que preferir  |
 *                                       |
 * ======================================+
 */

$conn = new Connection();
$userRepo = new UsuarioRepository($conn);

// Definindo o e-mail e o username
$user = new Usuario(
    'admin',            // Username
    'admin@email.com',  // Email
    'admin'            // Cargo !!NÃO ALTERE!!
);

// Se preferir, altere a senha aqui
// Ou deixe a padrão (admin)
$hashPWD = password_hash(
    'admin', // Senha
    PASSWORD_ARGON2ID
);


$user->setSenha($hashPWD);
$userRepo->create($user);
