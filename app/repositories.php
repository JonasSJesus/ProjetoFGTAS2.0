<?php

use DI\ContainerBuilder;
use Fgtas\Database\Connection;
use Fgtas\Repositories\Atendimentos\AtendimentoRepository;
use Fgtas\Repositories\Atendimentos\FormaAtendimentoRepository;
use Fgtas\Repositories\Atendimentos\PublicoRepository;
use Fgtas\Repositories\Atendimentos\TipoAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IFormaAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IPublicoRepository;
use Fgtas\Repositories\Interfaces\ITipoAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IUsuarioRepository;
use Fgtas\Repositories\Usuario\UsuarioRepository;
use function DI\autowire;

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        // Mapeando banco de dados
        Connection::class => autowire(Connection::class),

        // Mapeando os repositorios
        IAtendimentoRepository::class => autowire(AtendimentoRepository::class),
        IFormaAtendimentoRepository::class => autowire(FormaAtendimentoRepository::class),
        IPublicoRepository::class => autowire(PublicoRepository::class),
        ITipoAtendimentoRepository::class => autowire(TipoAtendimentoRepository::class),
        IUsuarioRepository::class => autowire(UsuarioRepository::class),
    ]);
};
