<?php
namespace Werkint\Bundle\FrontendMapperBundle\Configuration;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Helper service to build RequireJS configuration options from the Symfony
 * configuration.
 *
 * @author Kevin Montag <kevin@hearsay.it>
 */
class ConfigurationBuilder
{
    /**
     * @var string
     */
    protected $exportPath;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var NamespaceMappingInterface
     */
    protected $mapping;

    /**
     * An array of options
     *
     * @var array
     */
    protected $options = [];

    /**
     * The constructor method
     *
     * @param ContainerInterface        $container
     * @param NamespaceMappingInterface $mapping
     * @param array                     $exportPaths The base path, where files will placed
     */
    public function __construct(
        ContainerInterface $container,
        NamespaceMappingInterface $mapping,
        array $exportPaths
    ) {
        $this->container = $container;
        $this->mapping = $mapping;
        $this->exportPathJs = ltrim($exportPaths['js'], '/');
        $this->exportPathScss = ltrim($exportPaths['scss'], '/');
        $this->exportPathSass = ltrim($exportPaths['sass'], '/');
    }

    /**
     * Adds the option
     *
     * @param string $name The option name
     * @param mixed  $value The option value
     */
    public function addOption($name, $value)
    {
        $this->options[$name] = $value;
    }
}
