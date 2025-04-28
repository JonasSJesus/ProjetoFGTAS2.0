<?php


use DI\ContainerBuilder;
use Dompdf\Dompdf;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;
use SlimSession\Helper as SessionHelper;

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        // Registre dependencias aqui

        SessionHelper::class => function (ContainerInterface $c) {
            return new SessionHelper();
        },

        ResponseFactoryInterface::class => function (ContainerInterface $container) {
            return $container->get(ResponseFactory::class);
        },

        Twig::class => function () {
            return Twig::create(ROOT_APP . '/resources', ['cache' => false]);
        },

        Dompdf::class => function () {
            return new Dompdf([
                // PDF Settings
            ]);
        }

    ]);
};
