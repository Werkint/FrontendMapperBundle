<?php
namespace Tommy\Bundle\JsTemplatingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
//todo: remove?
/**
 * AsseticFiltersPass.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class AsseticFiltersPass implements
    CompilerPassInterface
{
    const SASS_SRV = 'assetic.filter.sass';
    const SASS_CLASS = 'Tommy\Bundle\JsTemplatingBundle\Service\Filter\SassFilter';
    const SCSS_SRV = 'assetic.filter.scss';
    const SCSS_CLASS = 'Tommy\Bundle\JsTemplatingBundle\Service\Filter\ScssFilter';

    /**
     * {@inheritdoc}
     */
    public function process(
        ContainerBuilder $container
    ) {
//        $gemPath = $container->getParameter('tommy_js_templating')['gempath'];
//
//        $container->setParameter(
//            static::SASS_SRV . '.class',
//            static::SASS_CLASS
//        );
//        $sass = $container->getDefinition(static::SASS_SRV);
//        $sass->replaceArgument(0, $gemPath);
//        $sass->addArgument($container->getParameter('kernel.debug'));
//        $sass->addArgument('UTF-8');
//
//        $container->setParameter(
//            static::SCSS_SRV . '.class',
//            static::SCSS_CLASS
//        );
//        $scss = $container->getDefinition(static::SCSS_SRV);
//        $scss->replaceArgument(0, $gemPath);
//        $scss->addArgument($container->getParameter('kernel.debug'));
//        $scss->addArgument('UTF-8');
//
//        $loadPaths = $container->getParameter('tommy_js_templating.load_paths');
//        foreach ($loadPaths as $path) {
//            $sass->addMethodCall('addLoadPath', [$path]);
//            $scss->addMethodCall('addLoadPath', [$path]);
//        }
    }
}
