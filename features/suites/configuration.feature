# language: fr
Fonctionnalité: J'instancie mon bundle puis le configure

Scénario: Configurer ElasticsearchBundle comme il faut
    Etant donné que je crée un nouveau kernel de test
    Quand       je configure le kernel avec le fichier "config/good.php"
    Et          je boot le kernel
    Alors       ca devrait s'être bien déroulé
    Et          les paramêtres de mon application devraient être :
    """
    {
        "elasticsearch.names": ["contract", "auth"],
        "elasticsearch.contract.host": "http://mysql.etna.localhost:9200/contractmanager",
        "elasticsearch.contract.types": ["contract", "company"],
        "elasticsearch.contract.indexer": "TestApp\\Utils\\Indexers\\ContractIndexer",
        "elasticsearch.contract.server": "http://mysql.etna.localhost:9200/",
        "elasticsearch.contract.index": "contractmanager",
        "elasticsearch.contract.configuration_path": "/Users/dubost_g/ETNA/composer-elasticsearch-service-provider/TestApp/config/elasticsearch/contract",
        "elasticsearch.auth.host": "http://mysql.etna.localhost:9200/auth",
        "elasticsearch.auth.types": ["user"],
        "elasticsearch.auth.indexer": "TestApp\\Utils\\Indexers\\ContractIndexer",
        "elasticsearch.auth.server": "http://mysql.etna.localhost:9200/",
        "elasticsearch.auth.index": "auth",
        "elasticsearch.auth.configuration_path": "/Users/dubost_g/ETNA/composer-elasticsearch-service-provider/TestApp/config/elasticsearch/auth"
    }
    """
    Et          le service "elasticsearch.elasticsearch_service" devrait exister
    Et          je n'ai plus besoin du kernel de test

Plan du Scénario: Ne pas configurer ElasticsearchBundle comme il faut
    Etant donné que je crée un nouveau kernel de test
    Quand       je configure le kernel avec le fichier "config/<file>"
    Et          je boot le kernel
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "<message>"
    Et          je n'ai plus besoin du kernel de test

    Exemples:
        | file                       | message                                                                                 |
        | no_parameter_path.php      | The child node "parameters_path" at path "elasticsearch" must be configured.            |
        | no_instances.php           | The child node "instances" at path "elasticsearch" must be configured.                  |
        | empty_instances.php        | The path "elasticsearch.instances" should have at least 1 element(s) defined.           |
        | no_instance_name.php       | The path "elasticsearch.instances.0.name" cannot contain an empty value, but got "".    |
        | no_instance_host.php       | The path "elasticsearch.instances.0.host" cannot contain an empty value, but got "".    |
        | no_instance_indexer.php    | The path "elasticsearch.instances.0.indexer" cannot contain an empty value, but got "". |
        | no_instance_types.php      | The child node "types" at path "elasticsearch.instances.0" must be configured.          |
        | empty_instance_type.php    | The path "elasticsearch.instances.0.types" should have at least 1 element(s) defined.   |
        | invalid_instance_type.php  | The path "elasticsearch.instances.0.types.1" cannot contain an empty value, but got "". |
        | unexisting_config_path.php | Elasticsearch commentcatutrouvepasledossier config directory not found                  |

Scénario: Mal implémenter l'indexer
    Etant donné que je crée un nouveau kernel de test
    Quand       je configure le kernel avec le fichier "config/invalid_indexer.php"
    Et          je boot le kernel
    Et          ca devrait s'être bien déroulé
    Et          je force l'instanciation du service "elasticsearch.elasticsearch_service"
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Indexer for contract must extends ETNA\Elasticsearch\AbstractEtnaIndexer"
    Et          je n'ai plus besoin du kernel de test
