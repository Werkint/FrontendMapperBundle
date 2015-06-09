<?php
namespace Tommy\Bundle\JsTemplatingBundle\Service\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\Asset\PackageInterface;

/**
 * Преобразует ссылки на другие бандлы в assetic'е
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class CssurlsFilter implements
    FilterInterface
{
    protected $kernel;
    protected $assetic;

    /**
     * @param KernelInterface  $kernel
     * @param PackageInterface $assetic
     */
    public function __construct(
        KernelInterface $kernel,
        PackageInterface $assetic
    ) {
        $this->kernel = $kernel;
        $this->assetic = $assetic;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $content = $asset->getContent();
        $content = preg_replace_callback('|(url)\((["\']?)@([^\)"\']+)(["\']?)\)|i', function (
            $matches
        ) use ($asset) {
            $tmpPath = $this->checkForBundleLinking($matches[3]);
            if ($tmpPath != null) {
                return $matches[1] . '(' . $matches[2] . $tmpPath . $matches[4] . ')';
            }

            return $matches[1] . '(' . $matches[2] . $matches[3] . $matches[4] . ')';
        }, $content);
        $asset->setContent($content);
    }

    /**
     * @param string $path
     * @return null|string
     */
    protected function checkForBundleLinking($path)
    {
        try {
            $path = explode('/', $path);
            $this->kernel->getBundle($path[0]);

            $url = 'bundles/';
            $url .= strtolower(substr($path[0], 0, -strlen('bundle')));
            array_shift($path);
            $url .= '/' . join('/', $path);

            return $this->assetic->getUrl($url);
        } catch (\InvalidArgumentException $e) {
            // Если бандл не найден
        }

        return null;
    }
}

