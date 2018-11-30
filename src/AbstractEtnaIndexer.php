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
 * Il faut donc surcharcher les fonctions putDocument, removeDocument et toutes celles dépendant des types gérés
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
     * @param string $type Type elasticsearch du document
     * @param int    $id   ID du document
     */
    public function indexOne($type, $id): void
    {
        if (false === in_array($type, $this->container->getParameter("elasticsearch.{$this->name}.types"))) {
            throw new \Exception("Invalid type {$type} for index {$this->name}");
        }
        $index_one_func_name = 'indexOne' . implode('', array_map('ucfirst', explode('_', $type)));
        if (!method_exists($this, $index_one_func_name)) {
            throw new \Exception("Implement the method {$index_one_func_name} as protected to index one type {$type}");
        }
        $this->{$index_one_func_name}($id);
    }

    /**
     * Fonction permettant l'indexation de tout les documents concernés par le(s) type(s) concernés.
     *
     * @param array $types Liste des différents types Elasticsearch à indexer
     */
    public function reindex($types = []): void
    {
        $all_types = $this->container->getParameter("elasticsearch.{$this->name}.types");
        if (!empty($invalid_types = array_diff($types, $all_types))) {
            throw new \Exception('Invalid type(s) ' . implode(', ', $invalid_types) . " for index {$this->name}");
        }

        if (empty($types)) {
            $types = $all_types;
        }

        foreach ($types as $type) {
            $index_func_name = 'index' . implode('', array_map('ucfirst', explode('_', $type)));
            if (!method_exists($this, $index_func_name)) {
                throw new \Exception("Implement the method {$index_func_name} as protected to index type {$type}");
            }
            $this->{$index_func_name}();
        }
    }

    /**
     * Cette s'occupe de faire l'appel HTTP pour indexer un document précis.
     *
     * @param string $type Le type elasticsearch
     *
     * @return array
     */
    abstract public function putDocument($type): array;

    /**
     * Cette s'occupe de faire l'appel HTTP pour supprimer un document précis.
     *
     * @param string $type Le type elasticsearch
     *
     * @return array
     */
    abstract public function removeDocument($type): array;
}
