<?php
namespace Tommy\Bundle\JsTemplatingBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Tommy\Bundle\JsTemplatingBundle\Exception\InvalidArgumentException;

/**
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class Util
{
    /**
     * Helper to convert bundle-notation paths to filesystem paths.
     *
     * @param string             $path
     * @param ContainerInterface $container
     * @return string
     * @throws InvalidArgumentException
     */
    public static function getRealPath($path, ContainerInterface $container)
    {
        // Expand bundle notation (snagged from the Assetic bundle)
        if ($path[0] == '@' && strpos($path, '/') !== false) {
            // Extract the bundle name and the directory within the bundle
            $bundle = substr($path, 1);
            $directory = '';

            if (($pos = strpos($bundle, '/')) !== false) {
                $directory = substr($bundle, $pos);
                $bundle = substr($bundle, 0, $pos);
            }

            // Get loaded bundles
            $bundles = $container->getParameter('kernel.bundles');

            // Reconstruct the path
            if (isset($bundles[$bundle])) {
                $rc = new \ReflectionClass($bundles[$bundle]);
                $path = dirname($rc->getFileName()) . $directory;
            } else {
                throw new InvalidArgumentException(sprintf('Unrecognized bundle: "%s"', $bundle));
            }
        }

        if (!is_file($path) && is_file($path . '.js')) {
            $path .= '.js';
        }

        return $path;
    }
} 