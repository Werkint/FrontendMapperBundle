<?php
namespace Tommy\Bundle\JsTemplatingBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;
use Tommy\Bundle\JsTemplatingBundle\DependencyInjection\Compiler\AsseticFiltersPass;
use Tommy\Bundle\JsTemplatingBundle\DependencyInjection\Compiler\JsmodelProviderPass;

/**
 * Bundle providing RequireJS integration.
 *
 * @author Kevin Montag <kevin@hearsay.it>
 * @codeCoverageIgnore
 */
class TommyJsTemplatingBundle extends Bundle
{
    private $kernel;

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
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new JsmodelProviderPass($this->kernel));
    }
}
