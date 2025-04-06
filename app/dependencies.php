<?php


use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
use SlimSession\Helper;

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        // Registre dependencias aqui
        Helper::class => function (ContainerInterface $c) {
            return new Helper();
        },

        ResponseFactoryInterface::class => function (ContainerInterface $container) {
            return $container->get(ResponseFactory::class);
        },
    ]);
};
