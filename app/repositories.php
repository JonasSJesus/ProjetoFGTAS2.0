<?php

use DI\ContainerBuilder;
use Fgtas\Repositories\Atendimentos\AtendimentoRepository;
use Fgtas\Repositories\FormaAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IFormaAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IPublicoRepository;
use Fgtas\Repositories\Interfaces\ITipoAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IUsuarioRepository;
use Fgtas\Repositories\PublicoRepository;
use Fgtas\Repositories\TipoAtendimentoRepository;
use Fgtas\Repositories\UsuarioRepository;
use function DI\autowire;

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        // Mapeando banco de dados
        PDO::class => function (ContainerBuilder $c) {
            $pdo = new PDO(
                "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD'], [
                    // Colocar as configuracoes do PDO aqui!
            ]);

            return $pdo;
            },

        // Mapeando os repositorios
        IAtendimentoRepository::class => autowire(AtendimentoRepository::class),
        IFormaAtendimentoRepository::class => autowire(FormaAtendimentoRepository::class),
        IPublicoRepository::class => autowire(PublicoRepository::class),
        ITipoAtendimentoRepository::class => autowire(TipoAtendimentoRepository::class),
        IUsuarioRepository::class => autowire(UsuarioRepository::class),
    ]);
};
