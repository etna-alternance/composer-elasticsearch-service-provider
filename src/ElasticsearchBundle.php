<?php
/**
 * PHP version 7.1
 *
 * @author BLU <dev@etna-alternance.net>
 */

declare(strict_types=1);

namespace ETNA\Elasticsearch;

use Symfony\Component\Console\Application;

/**
 * Classe de définition du Bundle.
 */
class ElasticsearchBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * Override de la fonction registerCommands pour générer une instance de commande par index.
     *
     * @param Application $application L'application symfony
     */
    public function registerCommands(Application $application): void
    {
        $application->add(new Command\IndexCommand());
        foreach ($this->container->getParameter('elasticsearch.names') as $name) {
            $application->add(new Command\SpecificIndexCommand($name));
        }
    }
}
