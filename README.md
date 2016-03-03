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
putenv("APP_NAME_ELASTICSEARCH_HOST=http://elasticsearch.etna-alternance.eu:9200/app_name");
putenv("APP_NAME_ELASTICSEARCH_TYPE=my_type");
```

###Register

Créer une classe `IndexElasticsearch` qui hérite de `AbstractEtnaIndexer`

Puis dans le fichier de configuration :

```
$app["elasticsearch.indexer"] = new IndexElasticsearch($app);
$app->register(new ETNA\Silex\Provider\Elasticsearch\ElasticSearch(['app_name']));
```
