<?php


use Dotenv\Dotenv;
use Fgtas\Database\Connection;
use Fgtas\Entities\Usuario;
use Fgtas\Repositories\Usuario\UsuarioRepository;

require_once "vendor/autoload.php";
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$conn = new Connection();
$userRepo = new UsuarioRepository($conn);
$user = new Usuario('admin', 'admin@email.com', 'admin');

$hashPWD = password_hash('admin', PASSWORD_ARGON2ID);

$user->setSenha($hashPWD);

$userRepo->create($user);
