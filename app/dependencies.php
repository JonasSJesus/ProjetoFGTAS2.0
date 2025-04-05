<?php


use DI\ContainerBuilder;

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        PDO::class =>
    ]);
};
