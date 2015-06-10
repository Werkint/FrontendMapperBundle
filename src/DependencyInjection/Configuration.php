<?php
namespace Werkint\Bundle\FrontendMapperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bundle configuration definitions.
 *
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Configuration implements
    ConfigurationInterface
{
    protected $alias;

    /**
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        // @formatter:off
        $treeBuilder
            ->root($this->alias)
            ->children()
                ->arrayNode('base_dir')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('js')->defaultValue('web/res/js')->end()
                        ->scalarNode('scss')->end()
                        ->scalarNode('sass')->end()
                    ->end()
                ->end()
                ->arrayNode('base_libs')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('js')->end()
                        ->scalarNode('sass')->end()
                        ->scalarNode('scss')->end()
                    ->end()
                ->end()
                ->booleanNode('auto_dump')->defaultFalse()->end()
                ->booleanNode('use_symlinks')->defaultFalse()->end()
                ->scalarNode('json_file')->defaultValue('bundles.js-model.json')->end()
                ->arrayNode('options')
                    ->defaultValue([])
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->always(function ($v) {
                                return ['value' => $v];
                            })
                        ->end()
                        ->children()
                            ->variableNode('value')
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        // @formatter:on

        return $treeBuilder;
    }
}
