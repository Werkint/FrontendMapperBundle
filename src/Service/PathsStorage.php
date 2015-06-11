<?php
namespace Werkint\Bundle\FrontendMapperBundle\Service;

use Werkint\Bundle\FrontendMapperBundle\Exception\PathNotFoundException;

/**
 * Сохраняет пути к файлам
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 *
 * TODO: кеш
 */
class PathsStorage
{
    const NS_FRONTEND = 'frontend';
    const NS_BOWER = 'bower';

    /**
     * @var array
     */
    protected $data;

    /**
     * {@inheritDoc}
     */
    public function registerPath($namespace, $path, array $metadata = [])
    {
        if (!$realPath = $this->getRealPath($path)) {
            throw new PathNotFoundException(
                sprintf('The path `%s` was not found.', $path)
            );
        }

        $this->data[$namespace][] = [
            'path'     => $path,
            'metadata' => $metadata,
        ];
    }

    /**
     * @return array
     */
    public function getRegisteredPaths($namespace)
    {
        return $this->data[$namespace];
    }

    /**
     * Gets the real path of the given path
     *
     * @param  string $path         The path
     * @return boolean|string       Returns false on failure, e.g. if the file
     *                              does not exist, or a string that represents
     *                              the real path of the given path
     */
    protected function getRealPath($path)
    {
        if (!$realPath = realpath($path)) {
            return false;
        }

        return $realPath;
    }
}
