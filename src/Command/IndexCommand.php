<?php
/**
 * Définition de la commande IndexCommand.
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
 * Classe définissant la commande qui va permettre l'indexation de nos données dans l'elasticsearch.
 *
 * Les paramêtres sont
 *  - --index-name|-i [index] Optionnel : permet de choisir l'index concerné
 *  - --reset                 Optionnel : permet de supprimer l'index et de le recréer avant d'indexer
 */
class IndexCommand extends ContainerAwareCommand
{
    /**
     * Configuration de la commande.
     *
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('elasticsearch:index')
             ->setDescription('Indexing elasticsearch indexes')
             ->addOption('index-name', 'i', InputOption::VALUE_OPTIONAL, 'Name of the index')
             ->addOption('reset', null, null, 'Do you want to reset the index first?')
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
        $container  = $this->getContainer();
        $elastic    = $container->get('elasticsearch.elasticsearch_service');
        $reset      = $input->getOption('reset');
        $index_name = $input->getOption('index-name');
        $action     = $reset ? 'Reindexing' : 'Indexing';

        $index_names = isset($index_name) ? [$index_name] : $container->getParameter('elasticsearch.names');
        foreach ($index_names as $index_name) {
            $output->writeln("<info>{$action} {$index_name}...</info>");
            $elastic->createIndex($index_name, $reset);
            $elastic->getIndexer($index_name)->reindex();
        }
        $output->writeln('<info>Done</info>');
    }
}
