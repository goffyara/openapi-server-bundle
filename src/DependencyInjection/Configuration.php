<?php


namespace OnMoon\OpenApiServerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('open_api_server');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('root_path')->end()
                ->scalarNode('root_name_space')->defaultValue('App\Generated')->cannotBeEmpty()->end()
                ->scalarNode('language_level')->defaultValue('7.4.0')->cannotBeEmpty()->end()
                ->scalarNode('generated_dir_permissions')->defaultValue('0755')->cannotBeEmpty()->end()
                ->arrayNode('specs')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                            ->enumNode('type')->values(['yaml','json'])->end()
                            ->scalarNode('name_space')->isRequired()->cannotBeEmpty()->end()
                            ->enumNode('media_type')
                                ->values(['application/json'])
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}