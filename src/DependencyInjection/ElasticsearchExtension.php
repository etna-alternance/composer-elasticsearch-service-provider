<?php
/**
 * Définition de la classe ElasticsearchExtension.
 *
 * @author BLU <dev@etna-alternance.net>
 *
 * @version 3.0.0
 */

declare(strict_types=1);

namespace ETNA\Elasticsearch\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * On définit cette classe pour personnaliser le processus de parsing de la configuration de notre bundle.
 *
 * Entre autres on ajoute la configuration dans les paramêtres du container Symfony
 */
class ElasticsearchExtension extends Extension
{
    /**
     * Cette fonction est appelée par symfony et permet le chargement de la configuration du bundle
     * Ici on va chercher la config des services dans le dossier Resources/config.
     *
     * @param array            $configs   Les éventuels paramètres
     * @param ContainerBuilder $container Le container de la configuration
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $all_names = [];
        foreach ($config['instances'] as $instance_conf) {
            $name        = $instance_conf['name'];
            $all_names[] = $name;

            $container->setParameter("elasticsearch.{$name}.host", $instance_conf['host']);
            $container->setParameter("elasticsearch.{$name}.types", $instance_conf['types']);
            $container->setParameter("elasticsearch.{$name}.indexer", $instance_conf['indexer']);

            $parsed_url = parse_url($instance_conf['host']);
            $index      = ltrim($parsed_url['path'], '/');
            $server     = str_replace($parsed_url['path'], '', $instance_conf['host']) . '/';

            $container->setParameter("elasticsearch.{$name}.server", $server);
            $container->setParameter("elasticsearch.{$name}.index", $index);

            $config_path = realpath("{$config['parameters_path']}/$name");

            if (false === $config_path || !is_dir($config_path)) {
                throw new \Exception("Elasticsearch {$name} config directory not found");
            }
            $container->setParameter("elasticsearch.{$name}.configuration_path", $config_path);
        }
        $container->setParameter('elasticsearch.names', $all_names);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');
    }
}
