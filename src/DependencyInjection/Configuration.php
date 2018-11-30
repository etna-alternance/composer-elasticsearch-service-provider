<?php
/**
 * PHP version 7.1
 *
 * @author BLU <dev@etna-alternance.net>
 */

declare(strict_types=1);

namespace ETNA\Elasticsearch\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Classe décrivant la configuration du AuthBundle.
 *
 * Exemple de configuration yaml :
 *
 * <pre>
 * elasticsearch:
 *   parameters_path: 'path/to/folder'
 *   instances:
 *     -
 *       name: index_name
 *       indexer: 'TestApp\Utils|FirstIndexer'
 *       host: http://elasticsearch.etna-alternance.eu:9200/index_name
 *       types:
 *         - type1
 *         - type2
 *     -
 *       name: other_index
 *       indexer: 'TestApp\Utils|OtherIndexer'
 *       host: http://elasticsearch.etna-alternance.eu:9200/other_index_name
 *       types:
 *         - type12
 * </pre>
 *
 * @example TestApp/config/packages/test/auth.php Exemple de configuration PHP
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Configure la structure de la configuration du AuthBundle.
     *
     * @return TreeBuilder Contient la config
     */
    public function getConfigTreeBuilder()
    {
        $tree_builder = new TreeBuilder();
        $root_node    = $tree_builder->root('elasticsearch');

        $root_node
            ->children()
                ->scalarNode('parameters_path')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('instances')
                    ->requiresAtLeastOneElement()
                    ->isRequired()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('indexer')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('host')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('types')
                                ->requiresAtLeastOneElement()
                                ->isRequired()
                                ->scalarPrototype()
                                    ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $tree_builder;
    }
}
