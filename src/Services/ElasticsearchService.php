<?php
/**
 * PHP version 7.1
 *
 * @author BLU <dev@etna-alternance.net>
 */

declare(strict_types=1);

namespace ETNA\Elasticsearch\Services;

use Elasticsearch\ClientBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Cette classe contient tout ce qu'il faut pour intéragir simplement avec elasticsearch au sein d'une application.
 *
 * Entre autres les clients HTTP, les classe Indexers, et les fonctions permettant la création des index/alias
 */
class ElasticsearchService
{
    /** @var ContainerInterface Conteneur de l'application symfony ou sont référencés les paramètres */
    private $container;

    /** @var array<string,\Elasticsearch\Client> La liste des différents clients Elasticsearch */
    private $clients;

    /** @var array<string,\ETNA\Elasticsearch\AbstractEtnaIndexer> Les différents permettant les indexations */
    private $indexers;

    /**
     * Constructeur du service.
     *
     * @param ContainerInterface $container Le container de l'application symfony
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        foreach ($container->getParameter('elasticsearch.names') as $name) {
            $this->clients[$name] = ClientBuilder::create()
                ->setHosts([$container->getParameter("elasticsearch.{$name}.server")])
                ->build();

            $indexer = $container->getParameter("elasticsearch.{$name}.indexer");

            if (false === is_subclass_of($indexer, "ETNA\Elasticsearch\AbstractEtnaIndexer")) {
                throw new \Exception("Indexer for {$name} must extends ETNA\Elasticsearch\AbstractEtnaIndexer");
            }

            $this->indexers[$name] = new $indexer($container, $name);
        }
    }

    /**
     * Retourne le client elasticsearch pour une instance configurée précise.
     *
     * @param string $elasticsearch_name Le nom de l'elasticsearch
     *
     * @return \Elasticsearch\Client Le client elasticsearch
     */
    public function getClient($elasticsearch_name): \Elasticsearch\Client
    {
        if (!isset($this->clients[$elasticsearch_name])) {
            throw new \Exception("There is no available client for {$elasticsearch_name}");
        }

        return $this->clients[$elasticsearch_name];
    }

    /**
     * Retourne l'indexer elasticsearch pour une instance configurée précise.
     *
     * @param string $elasticsearch_name Le nom de l'elasticsearch
     *
     * @return \ETNA\Elasticsearch\AbstractEtnaIndexer
     */
    public function getIndexer($elasticsearch_name): \ETNA\Elasticsearch\AbstractEtnaIndexer
    {
        if (!isset($this->indexers[$elasticsearch_name])) {
            throw new \Exception("There is no available indexer for {$elasticsearch_name}");
        }

        return $this->indexers[$elasticsearch_name];
    }

    /**
     * Crée l'index.
     *
     * @param string $name  Nom de l'instance ES configurée
     * @param bool   $reset Supprime et recrée l'index
     */
    public function createIndex($name, $reset = false): void
    {
        $container = $this->container;

        if (!\in_array($name, $container->getParameter('elasticsearch.names'))) {
            throw new \Exception("Application is not configured for index {$name}");
        }

        if (true === $reset) {
            $index_raw = $container->getParameter("elasticsearch.${name}.index");
            echo "\nCreating elasticsearch index... {$index_raw}\n";
            $this->unlock($name);

            $index = "{$index_raw}-" . $container->getParameter('version');
            $alias = [
                'index' => $index,
                'name'  => $container->getParameter("elasticsearch.{$name}.index"),
            ];
            try {
                $this->clients[$name]->indices()->deleteAlias($alias);
            } catch (\Exception $e) {
                echo "Alias doesn't exist... \n";
            }

            // On supprime l'index
            try {
                $this->clients[$name]->indices()->delete([
                    'index' => $index,
                ]);
            } catch (\Exception $exception) {
                echo "Index {$index} doesn't exist... \n";
            }

            $parameters_path = $container->getParameter("elasticsearch.{$name}.configuration_path");
            // On récupère les settings et les mappings pour créer l'index
            $settings = json_decode(file_get_contents("{$parameters_path}/settings.json"), true);

            $index_params = [
                'index' => $index,
                'body'  => ['settings' => $settings],
            ];
            // Création de l'index
            $this->clients[$name]->indices()->create($index_params);

            // Rajout de l'alias
            $this->clients[$name]->indices()->putAlias($alias);
            echo "Index {$index_raw} created successfully!\n\n";

            foreach ($container->getParameter("elasticsearch.{$name}.types") as $type) {
                self::createType($name, $type, $reset);
            }
        }
    }

    /**
     * Crée le mapping pour un type donné.
     *
     * @param string $name  Nom de l'instance concernée
     * @param string $type  Nom du type concerné
     * @param bool   $reset Supprime et recrée l'index
     */
    public function createType($name, $type, $reset = false): void
    {
        $container = $this->container;

        if (!\in_array($name, $container->getParameter('elasticsearch.names'))) {
            throw new \Exception("Application is not configured for index {$name}");
        }

        if (!\in_array($type, $container->getParameter("elasticsearch.{$name}.types"))) {
            throw new \Exception("Application is not configured for type {$type}");
        }

        if (true === $reset) {
            $index_raw = $container->getParameter("elasticsearch.${name}.index");

            echo "\nCreating ES type {$type} for index {$index_raw}\n";

            $parameters_path = $container->getParameter("elasticsearch.{$name}.configuration_path");
            if (!file_exists("{$parameters_path}/{$type}-mapping.json")) {
                throw new \Exception("Mapping file for type {$type} does not exist");
            }
            $mapping = json_decode(file_get_contents("{$parameters_path}/{$type}-mapping.json"), true);

            $this->unlock($name);

            try {
                $this->clients[$name]->indices()->deleteMapping([
                    'index' => $container->getParameter("elasticsearch.{$name}.index"),
                    'type'  => $type,
                ]);
            } catch (\Exception $exception) {
                echo "Type {$index_raw}/{$type} doesn't exist... \n";
            }

            $this->clients[$name]->indices()->putMapping([
                'index' => $container->getParameter("elasticsearch.{$name}.index"),
                'type'  => $type,
                'body'  => $mapping,
            ]);

            echo "Type {$index_raw}/{$type} created successfully!\n\n";
        }
    }

    /**
     * Passe l'index ES en read-only.
     *
     * @param string $name Nom de l'instance concernée
     */
    public function lock($name): void
    {
        $this->lockOrUnlockElasticSearch($name, 'lock');
    }

    /**
     * Passe l'index ES en read-write.
     *
     * @param string $name Nom de l'instance concernée
     */
    public function unlock($name): void
    {
        $this->lockOrUnlockElasticSearch($name, 'unlock');
    }

    /**
     * Bloque ou débloque les écritures sur l'elasticsearch.
     *
     * @param string $name   Nom de l'instance concernée
     * @param string $action "lock" ou "unlock" pour faire l'action qui porte le même nom
     */
    private function lockOrUnlockElasticSearch($name, $action): void
    {
        $action = ('lock' === $action) ? 'true' : 'false';

        if (!\in_array($name, $this->container->getParameter('elasticsearch.names'))) {
            throw new \Exception("Application is not configured for index {$name}");
        }

        $server = $this->container->getParameter("elasticsearch.{$name}.server")
            . $this->container->getParameter("elasticsearch.{$name}.index");
        exec(
            "curl -XPUT '" . $server . "/_settings' -d '
            {
                \"index\" : {
                    \"blocks.read_only\" : {$action}
                }
            }
            ' 2> /dev/null"
        );
    }
}
