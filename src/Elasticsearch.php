<?php

namespace ETNA\Silex\Provider\Elasticsearch;

use Silex\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Elasticsearch\ClientBuilder;

/**
*
*/
class Elasticsearch implements ServiceProviderInterface
{
    private $es_options;
    private $app;

    /**
     * @param null|string[] $es_options
     */
    public function __construct(array $es_options = null)
    {
        $es_options = $es_options ?: [
            "default",
        ];

        $this->es_options = [];
        foreach ($es_options as $db_name) {
            $name              = strtoupper($db_name);
            $elasicsearch_host = getenv("{$name}_ELASTICSEARCH_HOST");
            $elasicsearch_type = getenv("{$name}_ELASTICSEARCH_TYPE");
            if (false === $elasicsearch_host) {
                throw new \Exception("{$name}_ELASTICSEARCH_HOST doesn't exist");
            }
            if (false === $elasicsearch_type) {
                throw new \Exception("{$name}_ELASTICSEARCH_TYPE doesn't exist");

            }
            $this->es_options[$db_name] = [
                "host"    => $elasicsearch_host,
                "type"    => $elasicsearch_type
            ];
        }
    }

    /**
     *
     * @{inherit doc}
     */
    public function register(Container $app)
    {
        $this->app = $app;

        if (!isset($app["application_path"])) {
            throw new \Exception('$app["application_path"] is not set');
        }

        // On vérifie qu'on a bien un indexer
        if (false === isset($app["elasticsearch.indexer"]) ||
            false === is_subclass_of($app["elasticsearch.indexer"], "ETNA\Silex\Provider\Config\AbstractETNAIndexer")) {
            throw new \Exception('You must provide $app["elasticsearch.indexer see AbstractETNAIndexer"]');
        }

        foreach ($this->es_options as $es_option) {
            $parsed_url = parse_url($es_option['host']);
            $index      = ltrim($parsed_url['path'], '/');

            $app["elasticsearch.server"] = str_replace($parsed_url['path'], '', $es_option['host']) . "/";
            $app["elasticsearch.index"]  = $index;
            $app["elasticsearch.type"]   = $es_option['type'];
            break;
        }

        $app["elasticsearch"] = ClientBuilder::create()
                ->setHosts([$app["elasticsearch.server"]])
                ->build();

        $app['elasticsearch.create_index'] = [$this, 'createIndex'];
        $app['elasticsearch.lock']         = [$this, 'lock'];
        $app['elasticsearch.unlock']       = [$this, 'unlock'];
    }

    public function createIndex($reset = false)
    {
        $app = $this->app;

        if (!isset($app["version"])) {
            throw new \Exception('$app["version"] is not set');
        }

        if (true === $reset) {
            echo "\nCreating elasticsearch index... {$app["elasticsearch.index"]}\n";
            $this->unlock();

            // On supprime l'index
            try {
                $app["elasticsearch"]->indices()->delete(
                    ["index" => "{$app["elasticsearch.index"]}-{$app["version"]}"]
                );
            } catch (\Exception $exception) {
                echo "Index {$app["elasticsearch.index"]}-{$app["version"]} doesn't exist... \n";
            }

            // On récupère les settings et le mapping pour créer l'index
            $settings = json_decode(file_get_contents($app["elasticsearch_settings_path"]), true);
            $mapping  = json_decode(file_get_contents($app["elasticsearch_mapping_path"]), true);

            $index_params = [
                "index" => "{$app["elasticsearch.index"]}-{$app["version"]}",
                "body"  => [
                    "settings" => $settings,
                    "mapping"  => $mapping
                ]
            ];

            // Création de l'index
            $app["elasticsearch"]->indices()->create($index_params);

            // Rajout de l'alias
            $alias = [
                "index" => "{$app["elasticsearch.index"]}-{$app["version"]}",
                "name"  => $app["elasticsearch.index"]
            ];

            try {
                $app["elasticsearch"]->indices()->deleteAlias($alias);
            } catch (\Exception $e) {
                echo "Alias doesn't exist... \n";
            }
            $app["elasticsearch"]->indices()->putAlias($alias);

            echo "Index {$app["elasticsearch.index"]} created successfully!\n\n";
        }
    }

    public function lock()
    {
        $this->lockOrUnlockElasticSearch("lock");
    }

    public function unlock()
    {
        $this->lockOrUnlockElasticSearch("unlock");
    }

    /**
     * Bloque ou débloque les écritures sur l'elasticsearch
     *
     * @param string $action "lock" ou "unlock" pour faire l'action qui porte le même nom
     */
    private function lockOrUnlockElasticSearch($action)
    {
        switch (true) {
            case false === isset($this->app):
            case false === isset($this->app["elasticsearch.server"]):
            case false === isset($this->app["elasticsearch.index"]):
                throw new \Exception(__METHOD__ . "::{$action}: Missing parameter");
        }

        $action = ("lock" === $action) ? "true" : "false";

        $server = $this->app["elasticsearch.server"] . $this->app["elasticsearch.index"];
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
