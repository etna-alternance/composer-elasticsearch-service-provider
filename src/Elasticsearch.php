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
            $name                = strtoupper($db_name);
            $elasicsearch_host   = getenv("{$name}_ELASTICSEARCH_HOST");
            if (false === $elasicsearch_host) {
                throw new \Exception("{$name}_ELASTICSEARCH_HOST doesn't exist");
            }
            $this->es_options[$db_name] = [
                "host" => $elasicsearch_host,
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

        $app["elasticsearch.names"] = array_keys($this->es_options);
        foreach ($this->es_options as $name => $es_option) {
            // On vérifie qu'on a bien un indexer
            if (false === isset($app["elasticsearch.{$name}.indexer"]) ||
                false === is_subclass_of($app["elasticsearch.{$name}.indexer"], "ETNA\Silex\Provider\Elasticsearch\AbstractEtnaIndexer")) {
                throw new \Exception('You must provide $app["elasticsearch.{$name}.indexer"] see AbstractEtnaIndexer');
            }

            $parsed_url = parse_url($es_option['host']);
            $index      = ltrim($parsed_url['path'], '/');

            $app["elasticsearch.{$name}.server"] = rtrim(str_replace($parsed_url['path'], '', $es_option['host']), "/");
            $app["elasticsearch.{$name}.index"]  = $index;
            $app["elasticsearch.{$name}"] = ClientBuilder::create()
                ->setHosts([$app["elasticsearch.{$name}.server"]])
                ->build();
        }

        $app['elasticsearch.create_index'] = [$this, 'createIndex'];
        $app['elasticsearch.lock']         = [$this, 'lock'];
        $app['elasticsearch.unlock']       = [$this, 'unlock'];
    }

    public function createIndex($name, $reset = false)
    {
        $app = $this->app;

        if (!in_array($name, $app["elasticsearch.names"])) {
            throw new \Exception("Application is not configured for index {$name}");
        }

        if (!isset($app["version"])) {
            throw new \Exception('$app["version"] is not set');
        }

        if (true === $reset) {
            echo "\nCreating elasticsearch index... {$app["elasticsearch.$name.index"]}\n";
            $this->unlock($name);

            $alias = [
                "index" => "{$app["elasticsearch.$name.index"]}-{$app["version"]}",
                "name"  => $app["elasticsearch.{$name}.index"]
            ];
            try {
                $app["elasticsearch.{$name}"]->indices()->deleteAlias($alias);
            } catch (\Exception $e) {
                echo "Alias doesn't exist... \n";
            }

            // On supprime l'index
            try {
                $app["elasticsearch.{$name}"]->indices()->delete(
                    ["index" => "{$app["elasticsearch.$name.index"]}-{$app["version"]}"]
                );
            } catch (\Exception $exception) {
                echo "Index {$app["elasticsearch.$name.index"]}-{$app["version"]} doesn't exist... \n";
            }

            $parameters_path = $app["elasticsearch_{$name}_parameters_path"];
            // On récupère les settings et les mappings pour créer l'index
            $settings = json_decode(file_get_contents("$parameters_path/settings.json"), true);

            $index_params = [
                "index" => "{$app["elasticsearch.$name.index"]}-{$app["version"]}",
                "body"  => $settings
            ];
            // Création de l'index
            $app["elasticsearch.$name"]->indices()->create($index_params);
            // Rajout de l'alias
            $app["elasticsearch.{$name}"]->indices()->putAlias($alias);
            echo "Index {$app["elasticsearch.$name.index"]} created successfully!\n\n";
        }
    }

    public function lock($name)
    {
        $this->lockOrUnlockElasticSearch($name, "lock");
    }

    public function unlock($name)
    {
        $this->lockOrUnlockElasticSearch($name, "unlock");
    }

    /**
     * Bloque ou débloque les écritures sur l'elasticsearch
     *
     * @param string $action "lock" ou "unlock" pour faire l'action qui porte le même nom
     */
    private function lockOrUnlockElasticSearch($name, $action)
    {
        switch (true) {
            case false === isset($this->app):
            case false === isset($this->app["elasticsearch.{$name}.server"]):
            case false === isset($this->app["elasticsearch.{$name}.index"]):
                throw new \Exception(__METHOD__ . "::{$action}: Missing parameter");
        }

        $action = ("lock" === $action) ? "true" : "false";

        $server = $this->app["elasticsearch.{$name}.server"] . $this->app["elasticsearch.{$name}.index"];
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
