<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->extension("elasticsearch", array(
        "parameters_path" => __DIR__ . "/../elasticsearch",
        "instances" => [
            [
                "name" => "contract",
                "host" => "http://localhost:9200/contractmanager.test",
                "types" => ["contract", "company"],
                "indexer" => "TestApp\\Utils\\Indexers\\AppIndexer"
            ],
            [
                "name" => "auth",
                "host" => "http://localhost:9200/auth.test",
                "types" => ["user"],
                "indexer" => "TestApp\\Utils\\Indexers\\AppIndexer"
            ]
        ]
    ));
};
