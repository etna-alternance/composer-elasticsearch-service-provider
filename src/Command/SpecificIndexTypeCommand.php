<?php
/**
 * PHP version 7.1
 *
 * @author BLU <dev@etna-alternance.net>
 */

declare(strict_types=1);

namespace ETNA\Elasticsearch\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe définissant la commande qui va permettre l'indexation de nos données dans l'elasticsearch pour un type précis.
 *
 * Les paramêtres sont
 *  - --reset   Optionnel : permet de supprimer l'index et de le recréer avant d'indexer
 *  - --id [id] Optionnel : permet de choisir le document à indexer
 */
class SpecificIndexTypeCommand extends ContainerAwareCommand
{
    /** @var string Le nom de l'index concerné */
    private $index_name;

    /** @var string Le nom du type concerné */
    private $type;

    /**
     * Constructeur de la classe.
     *
     * @param string $index_name Le nom de l'index concerné
     * @param string $type       Le nom du type concerné
     */
    public function __construct($index_name, $type)
    {
        $this->index_name = $index_name;
        $this->type       = $type;

        parent::__construct();
    }

    /**
     * Configuration de la commande.
     *
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName("elasticsearch:index:{$this->index_name}:{$this->type}")
             ->setDescription("Indexing elasticsearch for {$this->index_name}/{$this->type}")
             ->addOption('reset', null, null, 'Do you want to reset the index first?')
             ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Id of the entity to index')
        ;
    }

    /**
     * Le code de la commande.
     *
     * @param InputInterface  $input  L'input de la commande (parametres, etc...)
     * @param OutputInterface $output L'output de la commande, un echo, mais en plus complet
     *
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var \ETNA\Elasticsearch\Services\ElasticsearchService */
        $elastic   = $container->get('elasticsearch.elasticsearch_service');
        /** @var bool */
        $reset     = $input->getOption('reset');
        /** @var int */
        $id        = $input->getOption('id');
        $action    = $reset ? 'Reindexing' : 'Indexing';

        if (!empty($id)) {
            $output->writeln("<info>Indexing document {$this->index_name}:{$this->type} {$id}...</info>");
            $elastic->getIndexer($this->index_name)->indexOne($this->type, $id);
        } else {
            $output->writeln("<info>{$action} {$this->index_name}:{$this->type}...</info>");
            $elastic->createType($this->index_name, $this->type, $reset);
            $elastic->getIndexer($this->index_name)->reindex([$this->type]);
        }
        $output->writeln('<info>Done</info>');

        return 0;
    }
}
