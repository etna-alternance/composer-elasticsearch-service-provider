<?php

namespace ETNA\Silex\Provider\Elasticsearch;

use Silex\Application;

abstract class AbstractEtnaIndexer
{
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app                                  = $app;
        $this->app['elasticsearch.reindex']         = [$this, 'reindex'];
        $this->app['elasticsearch.put_document']    = [$this, 'putDocument'];
        $this->app['elasticsearch.remove_document'] = [$this, 'removeDocument'];
    }

    /**
     * @return void
     */
    abstract public function reindex();

    /**
     * @return array
     */
    abstract public function putDocument();

    /**
     * @return void
     */
    abstract public function removeDocument();
}
