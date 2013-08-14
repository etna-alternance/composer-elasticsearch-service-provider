<?php

namespace ETNA\Silex\Provider\ElasticSearch;

use Guzzle\Http\Client;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * 
 */
class ElasticSearchServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $app["elasticsearch"] = $app->share(function ($app) {
            if (!isset($app["elasticsearch.server"])) {
                throw new \Exception('Undefined $app["elasticsearch.server"]');
            }
            if (!isset($app["elasticsearch.index"])) {
                throw new \Exception('Undefined $app["elasticsearch.index"]');
            }
            if (!isset($app["elasticsearch.type"])) {
                throw new \Exception('Undefined $app["elasticsearch.type"]');
            }
            return new Client("{$app["elasticsearch.server"]}{/index}{/type}/_search", [
                "index"  => $app["elasticsearch.index"],
                "type"   => $app["elasticsearch.type"],
            ]);
        });
    }
}
