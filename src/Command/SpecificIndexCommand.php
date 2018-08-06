<?php
/**
 * Définition de la commande SpecificIndexCommand.
 *
 * @author BLU <dev@etna-alternance.net>
 *
 * @version 3.0.0
 */

declare(strict_types=1);

namespace ETNA\Elasticsearch\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe définissant la commande qui va permettre l'indexation des données dans l'elasticsearch pour un index précis.
 *
 * Les paramêtres sont
 *  - --reset          Optionnel : permet de supprimer l'index et de le recréer avant d'indexer
 *  - --type|-t [type] Optionnel : permet de choisir le type de l'index concerné
 */
class SpecificIndexCommand extends ContainerAwareCommand
{
    /** @var string Le nom de l'index concerné */
    private $index_name;

    /**
     * Constructeur de la classe.
     *
     * @param string $index_name Le nom de l'index concerné
     */
    public function __construct($index_name)
    {
        $this->index_name = $index_name;
        parent::__construct();
    }

    /**
     * Configuration de la commande.
     *
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName("elasticsearch:index:{$this->index_name}")
             ->setDescription("Indexing elasticsearch for {$this->index_name}")
             ->addOption('reset', null, null, 'Do you want to reset the index first?')
             ->addOption('type', 't', InputOption::VALUE_OPTIONAL, 'Type to reindex')
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
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $container = $this->getContainer();
        $elastic   = $container->get('elasticsearch.elasticsearch_service');
        $reset     = $input->getOption('reset');
        $type      = $input->getOption('type');
        $action    = $reset ? 'Reindexing' : 'Indexing';

        if (isset($type)) {
            $output->writeln("<info>{$action} {$this->index_name}:{$type}...</info>");
            $elastic->createType($this->index_name, $type, $reset);
            $elastic->getIndexer($this->index_name)->reindex([$type]);
        } else {
            $output->writeln("<info>{$action} {$this->index_name}...</info>");
            $elastic->createIndex($this->index_name, $reset);
            $elastic->getIndexer($this->index_name)->reindex();
        }
        $output->writeln('<info>Done</info>');
    }
}
