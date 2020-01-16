<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use ETNA\FeatureContext\BaseContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends BaseContext
{
    /** @var CommandTester Le tester de command symfony */
    private $command_tester;

    public function __construct()
    {
    }

    /**
     * @Given je lance la commande ":command_name" avec les paramêtres contenus dans :command_params
     * @Given je lance la commande ":command_name"
     */
    public function jeLanceLaCommande($command_name, $command_params = null)
    {
        $application = new Application($this->getKernel());
        $command     = $application->find($command_name);
        $tester      = new CommandTester($command);
        $params      = [];

        if (null !== $command_params) {
            $params = json_decode(file_get_contents($this->requests_path . "/" . $command_params), true);
        }

        $this->tester = $tester;
        $this->getContext("ETNA\FeatureContext\ExceptionContainerContext")->try(
            function () use ($tester, $params) {
                $tester->execute($params);
            }
        );
    }

    /**
     * @Given la sortie de la commande devrait être identique à ":output_file"
     */
    public function laSortieDeLaCommandeDevraitEtreIdentiqueA($output_file)
    {
        $expected = file_get_contents($this->results_path . "/" . $output_file);

        if ($expected !== $this->tester->getDisplay()) {
            throw new \Exception("Unmaching command outputs : ===\n{$this->tester->getDisplay()}\n===\n$expected\n===\n");
        }
    }

    /**
     * @Given la commande ":command_name" devrait exister
     */
    public function laCommandeDevraitExister($command_name)
    {
        $application = new Application($this->getKernel());

        // Ca throw une exception si la commande n'existe pas
        $application->find($command_name);
    }

    /**
     * @Given j'utilise l'indexer :elastic_name pour indexer le document avec l'id :id
     */
    public function jutiliseLindexerPourIndexerLeDocumentAvecLid($elastic_name, $id)
    {
        $container = $this->getKernel()->getContainer();

        $this->getContext("ETNA\FeatureContext\ExceptionContainerContext")->try(
            function () use ($container, $elastic_name, $id) {
                $indexer = $container->get('elasticsearch.elasticsearch_service')->getIndexer($elastic_name);

                $indexer->indexOne($id);
            }
        );
    }

    /**
     * @Given j'utilise l'indexer :elastic_name pour indexer les documents
     */
    public function jutiliseLindexerPourIndexerLesDocuments($elastic_name)
    {
        $container = $this->getKernel()->getContainer();

        $this->getContext("ETNA\FeatureContext\ExceptionContainerContext")->try(
            function () use ($container, $elastic_name) {
                $indexer = $container->get('elasticsearch.elasticsearch_service')->getIndexer($elastic_name);

                $indexer->reindex();
            }
        );
    }

    /**
     * @Given je veux récupérer l'indexer pour l'elasticsearch :elasticname
     */
    public function jeVeuxRecupererLIndexerPourLElasticsearch($elastic_name)
    {
        $container = $this->getContainer();

        $this->getContext("ETNA\FeatureContext\ExceptionContainerContext")->try(
            function () use ($container, $elastic_name) {
                $container->get('elasticsearch.elasticsearch_service')->getIndexer($elastic_name);
            }
        );
    }

    /**
     * @Given je veux récupérer le client pour l'elasticsearch :elasticname
     */
    public function jeVeuxRecupererLeClientPourLElasticsearch($elastic_name)
    {
        $container = $this->getContainer();

        $this->getContext("ETNA\FeatureContext\ExceptionContainerContext")->try(
            function () use ($container, $elastic_name) {
                $container->get('elasticsearch.elasticsearch_service')->getClient($elastic_name);
            }
        );
    }

    /**
     * @Given je crée l'index sur l'elasticsearch :elastic_name
     */
    public function jeCreeLIndexSurLElasticsearch($elastic_name)
    {
        $container = $this->getContainer();

        $this->getContext("ETNA\FeatureContext\ExceptionContainerContext")->try(
            function () use ($container, $elastic_name) {
                $container->get('elasticsearch.elasticsearch_service')->createIndex($elastic_name);
            }
        );
    }

    /**
     * @Given je reset l'index sur l'elasticsearch :elastic_name
     */
    public function jeResetLIndexSurLElasticsearch($elastic_name)
    {
        $container = $this->getContainer();

        $this->getContext("ETNA\FeatureContext\ExceptionContainerContext")->try(
            function () use ($container, $elastic_name) {
                $container->get('elasticsearch.elasticsearch_service')->createIndex($elastic_name, true);
            }
        );
    }

    /**
     * @Given je delete l'alias de l'index de l'elasticsearch :elastic_name
     */
    public function jeDeleteLAliasDeLIndexDeLElasticsearch($elastic_name)
    {
        $container = $this->getContainer();
        $client    = $container->get('elasticsearch.elasticsearch_service')->getClient($elastic_name);
        $index     = "{$container->getParameter("elasticsearch.{$elastic_name}.index")}-{$container->getParameter('version')}";

        $client->indices()->deleteAlias([
            'index' => $index,
            'name'  => $container->getParameter("elasticsearch.{$elastic_name}.index"),
        ]);
    }

    /**
     * @Given je delete l'index de l'elasticsearch :elastic_name
     */
    public function jeDeleteLIndexDeLElasticsearch($elastic_name)
    {
        $container = $this->getContainer();
        $client    = $container->get('elasticsearch.elasticsearch_service')->getClient($elastic_name);
        $index     = "{$container->getParameter("elasticsearch.{$elastic_name}.index")}-{$container->getParameter('version')}";

        $client->indices()->delete([
            'index' => $index,
        ]);
    }

    /**
     * @Given je delete le mapping de l'elasticsearch :elastic_name
     */
    public function jeDeleteLeMappingDuTypeDeLElasticsearch($elastic_name)
    {
        $container = $this->getContainer();
        $client    = $container->get('elasticsearch.elasticsearch_service')->getClient($elastic_name);

        $client->indices()->deleteMapping([
            'index' => $container->getParameter("elasticsearch.{$elastic_name}.index"),
        ]);
    }

    /**
     * @Given je lock l'index de l'elasticsearch :elastic_name
     */
    public function jeLockLIndex($elastic_name)
    {
        $container = $this->getContainer();

        $this->getContext("ETNA\FeatureContext\ExceptionContainerContext")->try(
            function () use ($container, $elastic_name) {
                $container->get('elasticsearch.elasticsearch_service')->lock($elastic_name);
            }
        );
    }

    /**
     * @Given j'unlock l'index de l'elasticsearch :elastic_name
     */
    public function jUnlockLIndex($elastic_name)
    {
        $container = $this->getContainer();

        $this->getContext("ETNA\FeatureContext\ExceptionContainerContext")->try(
            function () use ($container, $elastic_name) {
                $container->get('elasticsearch.elasticsearch_service')->unlock($elastic_name);
            }
        );
    }

    private function getSettings($elastic_name)
    {
        $container = $this->getContainer();
        $client    = $container->get('elasticsearch.elasticsearch_service')->getClient($elastic_name);
        $index     = "{$container->getParameter("elasticsearch.{$elastic_name}.index")}-{$container->getParameter('version')}";

        $settings = $client->indices()->getSettings(
            ["index" => $container->getParameter("elasticsearch.{$elastic_name}.index")]
        );

        return $settings[$index];
    }

    private function getMapping($elastic_name)
    {
        $container = $this->getContainer();
        $client    = $container->get('elasticsearch.elasticsearch_service')->getClient($elastic_name);
        $index     = "{$container->getParameter("elasticsearch.{$elastic_name}.index")}-{$container->getParameter('version')}";

        $mapping = $client->indices()->getMapping(
            [
                "index" => $container->getParameter("elasticsearch.{$elastic_name}.index"),
            ]
        );

        return $mapping[$index];
    }

    /**
     * @Given l'elasticsearch :elastic_name ne devrait pas être lock
     */
    public function lElasticsearchNeDevraitPasEtreLock($elastic_name)
    {
        $settings = $this->getSettings($elastic_name);

        // Elasticsearch stocke ca sous forme de string, bien que ce soit un booleen
        if ("false" !== $settings["settings"]["index"]["blocks"]["read_only"]) {
            throw new \Exception("Elasticsearch {$index} isn't unlocked");
        }
    }

    /**
     * @Given l'elasticsearch :elastic_name devrait être lock
     */
    public function lElasticsearchDevraitEtreLock($elastic_name)
    {
        $settings = $this->getSettings($elastic_name);

        // Elasticsearch stocke ca sous forme de string, bien que ce soit un booleen
        if ("true" !== $settings["settings"]["index"]["blocks"]["read_only"]) {
            throw new \Exception("Elasticsearch {$index} isn't unlocked");
        }
    }

    /**
     * @Given les settings de l'elasticsearch :elastic_name devraient être identique à :settings_file
     */
    public function lesSettingsDeLElasticsearchDevraientEtreIdentiqueA($elastic_name, $settings_file)
    {
        // Afin que $settings soit une stdClass
        $settings = json_decode(json_encode($this->getSettings($elastic_name)));

        $expected = json_decode(file_get_contents($this->results_path . "/" . $settings_file));

        $this->check($expected, $settings, "result", $errors);
        $this->handleErrors($settings, $errors);
    }

    /**
     * @Given le mapping de l'elasticsearch :elastic_name devrait être identique à :mapping_file
     */
    public function leMappingDeLElasticsearchDevraitEtreIdentiqueA($elastic_name, $mapping_file)
    {
        // Afin que $settings soit une stdClass
        $mapping = json_decode(json_encode($this->getMapping($elastic_name)));

        $expected = json_decode(file_get_contents($this->results_path . "/" . $mapping_file));

        $this->check($expected, $mapping, "result", $errors);
        $this->handleErrors($mapping, $errors);
    }
}
