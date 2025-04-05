<?php

use DI\ContainerBuilder;
use Fgtas\Entities\FormaAtendimento;
use Fgtas\Repositories\AtendimentoRepository;
use Fgtas\Repositories\PublicoRepository;
use Fgtas\Repositories\TipoAtendimentoRepository;
use Fgtas\Repositories\UsuarioRepository;
use function DI\autowire;

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        // Mapeando os repositorios
        AtendimentoRepository::class => autowire(AtendimentoRepository::class),
        FormaAtendimento::class => autowire(FormaAtendimento::class),
        PublicoRepository::class => autowire(PublicoRepository::class),
        TipoAtendimentoRepository::class => autowire(TipoAtendimentoRepository::class),
        UsuarioRepository::class => autowire(UsuarioRepository::class),
    ]);
};
