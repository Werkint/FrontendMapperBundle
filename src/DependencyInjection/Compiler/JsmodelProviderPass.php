<?php
namespace Werkint\Bundle\FrontendMapperBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Werkint\Bundle\FrontendMapperBundle\Service\Util;

/**
 * Проходится по бандлам и генерирует провайдеры для фронтенда
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class JsmodelProviderPass implements
    CompilerPassInterface
{
    const FRONTEND_PATH = 'Resources/frontend';
    const EXT_NAME = 'werkint_frontend_mapper';
    const PARAMETER_POSTFIX = 'frontend_config';

    protected $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }


    /**
     * {@inheritdoc}
     */
    public function process(
        ContainerBuilder $container
    ) {
        foreach ($this->kernel->getBundles() as $bundle) {
            $config = $this->getBundleConfig($bundle, $container);

            if (is_string($config)) {
                $config = [[
                    'path' => $config,
                    'name' => $this->getBundleAlias($bundle),
                ]];
            }

            foreach ($config as $row) {
                $this->addMapping($row['path'], $row['name'], $container);
            }
        }
    }

    /**
     * @param BundleInterface $bundle
     * @param boolean         $forceLowDash
     * @return string
     */
    protected function getBundleAlias(
        BundleInterface $bundle,
        $forceLowDash = false
    ) {
        $dash = $forceLowDash ? '_' : '-';

        if (!$bundle->getContainerExtension()) {
            $alias = strtolower(preg_replace('/([a-z])([A-Z]+)/', '$1' . $dash . '$2', $bundle->getName()));
            return preg_replace('/^(.*?)' . $dash . '?bundle$/', '$1', $alias);
        }
        return str_replace('_', $dash, $bundle->getContainerExtension()->getAlias());
    }

    /**
     * @param BundleInterface  $bundle
     * @param ContainerBuilder $container
     * @return array|string
     * @throws \Exception
     */
    protected function getBundleConfig(
        BundleInterface $bundle,
        ContainerBuilder $container
    ) {
        $alias = $this->getBundleAlias($bundle, true);

        $parameter = $alias . '.' . static::PARAMETER_POSTFIX;
        if (!$container->hasParameter($parameter)) {
            $path = $bundle->getPath() . '/' . static::FRONTEND_PATH;
            if (!file_exists($path)) {
                return [];
            }
            $container->setParameter($parameter, $path);
        }

        $data = $container->getParameter($parameter);
        if (!in_array(gettype($data), ['array', 'string'])) {
            throw new \Exception('Wrong config parameter');
        }

        return $data;
    }

    /**
     * @param string           $path
     * @param string           $name
     * @param ContainerBuilder $container
     */
    protected function addMapping(
        $path,
        $name,
        ContainerBuilder $container
    ) {
        $location = Util::getRealPath($path, $container);

        // Register the namespace with the configuration
        $mapping = $container->getDefinition(static::EXT_NAME . '.namespace_mapping');
        $mapping->addMethodCall('registerNamespace', [$name, $location]);
    }
}
