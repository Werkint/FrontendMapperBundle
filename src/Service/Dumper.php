<?php
namespace Werkint\Bundle\FrontendMapperBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * @author Tomfun <tomfun1990@gmail.com>
 */
class Dumper implements CacheWarmerInterface
{
    /**
     * @var DumpProcessor
     */
    protected $processor;
    /**
     * @var bool
     */
    protected $autoDump;

    /**
     * @param bool $autoDump
     * @param DumpProcessor $dumpProcessor
     */
    public function __construct($autoDump, $dumpProcessor)
    {
        $this->autoDump = $autoDump;
        $this->processor = $dumpProcessor;
    }
    /**
     * @param string $cacheDir
     */
    public function warmUp($cacheDir)
    {
        if ($this->autoDump) {
            $this->processor->dump();
        }
    }

    /**
     * @return bool
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
