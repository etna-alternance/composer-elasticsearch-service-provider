<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->extension("elasticsearch", array(
        "parameters_path" => __DIR__ . "/../elasticsearch",
        "instances" => [
            [
                "name" => "contract",
                "host" => "http://elasticsearch:9200/contractmanager.test",
                "indexer" => "TestApp\\Utils\\Indexers\\AppIndexer"
            ],
            [
                "name" => "auth",
                "host" => "http://elasticsearch:9200/auth.test",
                "indexer" => "TestApp\\Utils\\Indexers\\AppIndexer"
            ]
        ]
    ));
};
