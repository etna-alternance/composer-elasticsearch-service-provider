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

 - Une variable <INDEX_NAME>_ELASTICSEARCH_HOST qui contient l'adresse et le nom de l'index ES
 - Une variable <INDEX_NAME>_ELASTICSEARCH_TYPES qui contient la liste des types de l'index séparés par une `,`

```
putenv("INDEX_NAME_ELASTICSEARCH_HOST=http://elasticsearch.etna-alternance.eu:9200/index_name");
putenv("INDEX_NAME_ELASTICSEARCH_TYPES=my_first_type,my_second_type");

putenv("OTHER_INDEX_NAME_ELASTICSEARCH_HOST=http://elasticsearch.etna-alternance.eu:9200/other_index_name");
putenv("OTHER_INDEX_NAME_ELASTICSEARCH_TYPES=my_other_type");
```

###Register

Pour chaque index différent créer une classe `IndexElasticsearch` qui hérite de `AbstractEtnaIndexer`
Il faut implémenter une foncion par type de l'index pour indexer son contenu.

Par exemple pour l'index `index_name` il faudra implémenter (comme dans l'exemple suivant) les fonctions :
 - indexMyFirstType
 - indexMySecondType

```
use ETNA\Silex\Provider\Elasticsearch\AbstractETNAIndexer;

use Silex\Application;

class IndexNameElasticsearchIndexer extends AbstractETNAIndexer
{
    public function __construct(Application $app)
    {
        parent::__construct($app, "index_name");
    }

    protected function indexMyFirstType()
    {
      //Code here
    }

    protected function indexMySecondType()
    {
      //Code here
    }

    public function putDocument($type, Entity $entity = null)
    {
      //Code here
    }

    public function removeDocument($type, Entity $entity = null)
    {
      //Code here
    }
}
```

Les paramêtres de l'index vont se chercher dans un dossier.
Pour indiquer à l'application ou est ce dossier :
```
$app["elasticsearch_index_name_parameters_path"] = "my/path/to/folder";
```

Ce dossier doit avoir l'arborescence suivante :
  - IndexNameParameters :
    - settings.json : Settings de l'index
    - my_first_type-mapping.json : Mapping pour le type my_first_type
    - my_second_type-mapping.json : Mapping pour le type my_second_type

Puis dans le fichier de configuration :

```
$app["elasticsearch.index_name.indexer"]       = new IndexNameElasticsearchIndexer($app);
$app["elasticsearch.other_index_name.indexer"] = new OtherIndexNameElasticsearchIndexer($app);
$app->register(new ETNA\Silex\Provider\Elasticsearch\ElasticSearch(['index_name', 'other_index_name']));
```
