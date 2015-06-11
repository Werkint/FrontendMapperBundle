<?php
namespace Werkint\Bundle\FrontendMapperBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;
use Werkint\Bundle\FrontendMapperBundle\DependencyInjection\Compiler\BowerProviderPass;
use Werkint\Bundle\FrontendMapperBundle\DependencyInjection\Compiler\JsmodelProviderPass;

/**
 * Bundle providing RequireJS integration.
 *
 * @author Kevin Montag <kevin@hearsay.it>
 * @codeCoverageIgnore
 */
class WerkintFrontendMapperBundle extends Bundle
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
        $container->addCompilerPass(new BowerProviderPass($this->kernel));
    }
}
