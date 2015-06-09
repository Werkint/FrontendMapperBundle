<?php
namespace Tommy\Bundle\JsTemplatingBundle\Configuration;

use Tommy\Bundle\JsTemplatingBundle\Exception\PathNotFoundException;

/**
 * Mapping of base file paths to Javascript module namespaces.
 *
 * @author Kevin Montag <kevin@hearsay.it>
 */
interface NamespaceMappingInterface
{
    /**
     * Registers a namespace to a filesystem path mapping
     *
     * @param  string $namespace The namespace
     * @param  string $path      The path
     * @param  string $type
     * @throws PathNotFoundException            If the path was not found
     */
    public function registerNamespace($namespace, $path, $type);

    /**
     * Gets the module path, e.g. `namespace/modules.js` corresponding to a
     * filesystem path
     *
     * @param  string $filename The filename
     * @return boolean|string           Returns false on failure, e.g. if the
     *                                  file does not exist or a string that
     *                                  represents the module path
     */
    public function getModulePath($filename);
}
