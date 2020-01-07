<?php

namespace ETNA\Silex\Provider\Elasticsearch;

use Silex\Application;

abstract class AbstractEtnaIndexer
{
    protected $app;
    protected $name;

    /**
     * @param Application $app
     */
    public function __construct(Application $app, $name)
    {
        $this->app  = $app;
        $this->name = $name;

        $this->app["elasticsearch.{$name}.reindex"]         = [$this, 'reindex'];
        $this->app["elasticsearch.{$name}.index_one"]       = [$this, 'indexOne'];
        $this->app["elasticsearch.{$name}.put_document"]    = [$this, 'putDocument'];
        $this->app["elasticsearch.{$name}.remove_document"] = [$this, 'removeDocument'];
    }

    /**
     * @return void
     */
    abstract public function reindex();

    /**
     * @param mixed $id
     *
     * @return array
     */
    abstract public function indexOne($id);

    /**
     * @return array
     */
    abstract public function putDocument();

    /**
     * @return void
     */
    abstract public function removeDocument();
}
