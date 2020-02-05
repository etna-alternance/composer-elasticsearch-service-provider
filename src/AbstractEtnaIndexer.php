<?php
/**
 * PHP version 7.1
 *
 * @author BLU <dev@etna-alternance.net>
 */

declare(strict_types=1);

namespace ETNA\Elasticsearch;

use Doctrine\ORM\Tools\Pagination\Paginator;
use ETNA\Doctrine\Entity\AbstractIndexableEntity;
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
     */
    abstract public function indexOne($id): void;

    /**
     * Fonction permettant l'indexation de tout les documents concernés de l'index.
     */
    abstract public function reindex(): void;

    /**
     * Récupère les résultats d'une query DQL et les indexe dans l'elasticsearch.
     *
     * @param string $dql La requête DQL
     */
    protected function indexQueryResult($dql): void
    {
        /** @var \Doctrine\ORM\EntityManager */
        $em           = $this->container->get('doctrine.orm.entity_manager');
        $min_id       = 0;
        $first_result = 0;
        $chunk_size   = 250;

        echo "Indexing {$this->name} .";
        do {
            $query = $em->createQuery($dql);

            $query
                ->setFirstResult($first_result)
                ->setMaxResults($chunk_size)
                ->setParameter('min_id', $min_id);
            $paginator = new Paginator($query);
            $count     = 0;

            foreach ($paginator as $entity) {
                $result = $this->putDocument($entity);
                if (!empty($result) && 'created' !== $result['result']) {
                    print_r($result);
                }
                ++$count;
                unset($entity);
                echo '.';
            }
            unset($paginator);
            $em->clear();
            $first_result += $chunk_size;
        } while ($count === $chunk_size);
        echo "\n";
    }

    /**
     * Cette s'occupe de faire l'appel HTTP pour indexer un document précis.
     *
     * @param object $entity L'entité à supprimer
     *
     * @return array
     */
    public function putDocument($entity = null): array
    {
        if (null === $entity || !is_a($entity, AbstractIndexableEntity::class)) {
            return [];
        }

        /** @var \ETNA\Elasticsearch\Services\ElasticsearchService */
        $service      = $this->container->get('elasticsearch.elasticsearch_service');
        $index_params = [
            'index' => $this->container->getParameter("elasticsearch.{$this->name}.index"),
            'id'    => $entity->getId(),
            'body'  => json_encode($entity->toIndex(), JSON_UNESCAPED_UNICODE),
        ];

        return $service->getClient($this->name)->index($index_params);
    }

    /**
     * Cette s'occupe de faire l'appel HTTP pour supprimer un document précis.
     *
     * @param object $entity L'entité à supprimer
     *
     * @return array
     */
    public function removeDocument($entity = null): array
    {
        if (null === $entity || !is_a($entity, AbstractIndexableEntity::class)) {
            return [];
        }

        /** @var \ETNA\Elasticsearch\Services\ElasticsearchService */
        $service      = $this->container->get('elasticsearch.elasticsearch_service');
        $index_params = [
            'index' => $this->container->getParameter("elasticsearch.{$this->name}.index"),
            'id'    => $entity->getId(),
        ];

        return $service->getClient($this->name)->delete($index_params);
    }
}
