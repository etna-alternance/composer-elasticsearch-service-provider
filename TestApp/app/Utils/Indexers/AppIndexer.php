<?php

namespace TestApp\Utils\Indexers;

class AppIndexer extends \ETNA\Elasticsearch\AbstractEtnaIndexer
{
    public function indexUser()
    {

    }

    public function indexOneUser()
    {

    }

    public function putDocument($type, $entity = null): array
    {
        return [];
    }

    public function removeDocument($type, $document_id = null): array
    {
        return [];
    }
}
