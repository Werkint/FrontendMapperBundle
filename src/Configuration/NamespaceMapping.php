<?php

namespace Tommy\Bundle\JsTemplatingBundle\Configuration;

use Tommy\Bundle\JsTemplatingBundle\Exception\PathNotFoundException;

/**
 * Concrete module namespace map.
 *
 * @author Kevin Montag <kevin@hearsay.it>
 */
class NamespaceMapping implements NamespaceMappingInterface
{
    /**
     * An internal namespace map
     *
     * @var array
     */
    protected $namespaces = [];

    /**
     * {@inheritDoc}
     */
    public function registerNamespace($namespace, $path, $type)
    {
        if (!$realPath = $this->getRealPath($path)) {
            throw new PathNotFoundException(
                sprintf('The path `%s` was not found.', $path)
            );
        }

        $this->namespaces[] = [$namespace, $realPath, $type];
    }

    /**
     * {@inheritDoc}
     */
    public function getModulePath($filename)
    {
        $filePath = $this->getRealPath($filename);

        foreach ($this->namespaces as $realPath) {
            $namespace = $realPath[0];
            $realPath = $realPath[1];
            $type = $realPath[2];
            if (strpos($filePath, $realPath) === 0) {
                $modulePath = $this->basePath . '/' . $namespace;

                if (is_file($filePath)) {
                    $modulePath .= '/' . $this->getBaseName($filePath, $realPath);
                }

                return preg_replace('~[/\\\\]+~', '/', $modulePath);
            }
        }

        return false;
    }

    /**
     * @return array type => [ ['path' => ..., 'exportName' => ...] ]
     */
    public function getRegisteredPaths()
    {
        $types = [];
        foreach ($this->namespaces as $item) {
            $types[] = $item[2];
        }
        $types = array_unique($types);
        $res = [];
        foreach ($types as $type) {
            $res[$type] = [];
            foreach ($this->namespaces as $item) {
                if ($item[2] === $type) {
                    $res[$type][] = ['path' => $item[1], 'exportName' => $item[0]];
                }
            }
        }
        return $res;
    }

    /**
     * Gets the base name of the given file path
     *
     * @param  string $filePath The file path
     * @param  string $realPath The real path of the namespace
     * @return string           Returns the base name of the given file path
     */
    protected function getBaseName($filePath, $realPath)
    {
        $basename = substr($filePath, strlen($realPath));

        if (!$basename) {
            $basename = basename($filePath);
        }

        // To allow to use the bundle with `.coffee` scripts
        return preg_replace('~\.coffee$~', '.js', $basename);
    }

    /**
     * Gets the real path of the given path
     *
     * @param  string $path The path
     * @return boolean|string       Returns false on failure, e.g. if the file
     *                              does not exist, or a string that represents
     *                              the real path of the given path
     */
    protected function getRealPath($path)
    {
        if (is_file($path . '.js')) {
            $path .= '.js';
        }

        if (!$realPath = realpath($path)) {
            return false;
        }

        return $realPath;
    }
}
