<?php
/**
 * Définition de la classe ElasticsearchBundle.
 *
 * Point d'entrée de ce bundle configure et intéragit avec une instance elasticsearch
 *
 * @author BLU <dev@etna-alternance.net>
 *
 * @version 3.0.0
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
     * Override de la fonction registerCommands pour générer une instance de commande par index puis par type.
     *
     * @param Application $application L'application symfony
     */
    public function registerCommands(Application $application): void
    {
        $application->add(new Command\IndexCommand());
        foreach ($this->container->getParameter('elasticsearch.names') as $name) {
            $application->add(new Command\SpecificIndexCommand($name));
            foreach ($this->container->getParameter("elasticsearch.{$name}.types") as $type) {
                $application->add(new Command\SpecificIndexTypeCommand($name, $type));
            }
        }
    }
}
