<?php
/**
 * PHP version 7.1
 *
 * @author BLU <dev@etna-alternance.net>
 */

declare(strict_types=1);

namespace ETNA\Elasticsearch;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Un indexer est une classe qui va nous permettre d'indéxer les différentes données de nos applications.
 *
 * Cette classe abstraite est à implémenter et provide des fonctions qui permettent de guider l'implémentation
 * d'elasticsearch dans nos applications.
 *
 * Il faut donc surcharger les fonctions putDocument, removeDocument, indexOne et reindex
 *
 * @abstract
 */
abstract class AbstractEtnaIndexer
{
    /** @var ContainerInterface Le conteneur de l'application Symfony */
    protected $container;

    /** @var string Le nom de l'instance Elasticsearch */
    protected $name;

    /**
     * Constructeur de la classe.
     *
     * @param ContainerInterface $container Le conteneur de l'application Symfony
     * @param string             $name      Le nom de l'instance Elasticsearch
     */
    public function __construct(ContainerInterface $container, $name)
    {
        $this->container = $container;
        $this->name      = $name;
    }

    /**
     * Fonction permettant l'indexation d'un document bien précis en renseignant l'id concerné.
     *
     * @param string $id ID du document
     *
     * @return void
     */
    abstract public function indexOne($id): void;

    /**
     * Fonction permettant l'indexation de tout les documents concernés de l'index.
     *
     * @return void
     */
    abstract public function reindex(): void;

    /**
     * Cette s'occupe de faire l'appel HTTP pour indexer un document précis.
     *
     * @return array
     */
    abstract public function putDocument(): array;

    /**
     * Cette s'occupe de faire l'appel HTTP pour supprimer un document précis.
     *
     * @return array
     */
    abstract public function removeDocument(): array;
}
