<?php

use DI\ContainerBuilder;
use Fgtas\Entities\FormaAtendimento;
use Fgtas\Repositories\AtendimentoRepository;
use Fgtas\Repositories\Interfaces\IUsuarioRepository;
use Fgtas\Repositories\PublicoRepository;
use Fgtas\Repositories\TipoAtendimentoRepository;
use Fgtas\Repositories\UsuarioRepository;
use function DI\autowire;

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        // Mapeando banco de dados
        PDO::class => function (ContainerBuilder $c) {
            return new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
        },

        // Mapeando os repositorios
        AtendimentoRepository::class => autowire(AtendimentoRepository::class),
        FormaAtendimento::class => autowire(FormaAtendimento::class),
        PublicoRepository::class => autowire(PublicoRepository::class),
        TipoAtendimentoRepository::class => autowire(TipoAtendimentoRepository::class),
        IUsuarioRepository::class => autowire(UsuarioRepository::class),
    ]);
};
