elasticsearch-service-provider
==============================

[![Dependency Status](https://www.versioneye.com/user/projects/53dde6e68e78abc191000030/badge.svg)](https://www.versioneye.com/user/projects/53dde6e68e78abc191000030)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/etna-alternance/composer-elasticsearch-service-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/etna-alternance/composer-elasticsearch-service-provider/?branch=master)

##Installation
------------

Modifier `composer.json` :

```
{
    // ...
    "require": {
        "etna/elasticsearch-service-provider": "~0.1"
    },
    "repositories": [
       {
           "type": "composer",
           "url": "http://blu-composer.herokuapp.com"
       }
   ]
}
```
##Configuration
-------------

###Environnement

Dans le fichier d'env :

```
putenv("INDEX_NAME_ELASTICSEARCH_HOST=http://elasticsearch.etna-alternance.eu:9200/index_name");
putenv("INDEX_NAME_ELASTICSEARCH_TYPE=my_type");

putenv("OTHER_INDEX_NAME_ELASTICSEARCH_HOST=http://elasticsearch.etna-alternance.eu:9200/other_index_name");
putenv("OTHER_INDEX_NAME_ELASTICSEARCH_TYPE=my_other_type");
```

###Register

Pour chaque index différent créer une classe `IndexElasticsearch` qui hérite de `AbstractEtnaIndexer`

```
use ETNA\Silex\Provider\Elasticsearch\AbstractETNAIndexer;

use Silex\Application;

class IndexNameElasticsearchIndexer extends AbstractETNAIndexer
{
    public function __construct(Application $app)
    {
        parent::__construct($app, "index_name");
    }

    public function reindex()
    {
      //Code here
    }

    public function putDocument(Entity $entity = null)
    {
      //Code here
    }

    public function removeDocument(Entity $entity = null)
    {
      //Code here
    }
}
```

Puis dans le fichier de configuration :

```
$app["elasticsearch.index_name.indexer"]       = new IndexNameElasticsearchIndexer($app);
$app["elasticsearch.other_index_name.indexer"] = new OtherIndexNameElasticsearchIndexer($app);
$app->register(new ETNA\Silex\Provider\Elasticsearch\ElasticSearch(['index_name', 'other_index_name']));
```
