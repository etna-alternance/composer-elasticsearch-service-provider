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
        "parameters_path" => __DIR__ . "/../../../../TestApp/config/elasticsearch",
        "instances" => [
            [
                "name" => "contract",
                "host" => "http://mysql.etna.localhost:9200/contractmanager",
                "indexer" => "OuaisOuais"
            ],
            [
                "name" => "auth",
                "host" => "http://mysql.etna.localhost:9200/auth",
                "indexer" => "TestApp\\Utils\\Indexers\\ContractIndexer"
            ]
        ]
    ));
};