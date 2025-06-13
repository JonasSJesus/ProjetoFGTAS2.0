<?php


use DI\ContainerBuilder;
use Dompdf\Dompdf;
use Fgtas\Helpers\SessionTwigExtension;
use Glazilla\Slim\Views\TwigMessages;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Flash\Messages;
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

        Twig::class => function (ContainerInterface $container) {
            // TODO: Implementar cache para as templates

            $twig = Twig::create(ROOT_APP . '/resources', ['cache' => false]);
            $twig->addExtension(new SessionTwigExtension());

            return $twig;
        },

        Dompdf::class => function () {
            return new Dompdf([
                // PDF Settings
            ]);
        },

        Messages::class => function () {
            $storage = [];
            return new Messages($storage);
        }

    ]);
};
