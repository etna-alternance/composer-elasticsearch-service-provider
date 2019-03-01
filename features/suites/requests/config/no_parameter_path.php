<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->parameters()->set("application_name", "Super appli");
    $container->parameters()->set("version", "1.4.2");

    $container->extension("framework", [
        "secret" => 'pouet',
        "test"   => true
    ]);

    $container->extension("elasticsearch", array(
        "instances" => [
            [
                "name" => "contract",
                "host" => "http://mysql.etna.localhost:9200/contractmanager",
                "types" => ["contract", "company"],
                "indexer" => "TestApp\\Utils\\Indexers\\ContractIndexer"
            ],
            [
                "name" => "haute",
                "host" => "http://mysql.etna.localhost:9200/auth",
                "types" => ["user"],
                "indexer" => "TestApp\\Utils\\Indexers\\ContractIndexer"
            ]
        ]
    ));
};
