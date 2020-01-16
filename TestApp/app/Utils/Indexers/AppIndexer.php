<?php

namespace TestApp\Utils\Indexers;

class AppIndexer extends \ETNA\Elasticsearch\AbstractEtnaIndexer
{
    public function indexOne($id): void
    {

    }

    public function reindex(): void
    {

    }

    public function putDocument($entity = null): array
    {
        return [];
    }

    public function removeDocument($document_id = null): array
    {
        return [];
    }
}
