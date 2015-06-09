<?php
namespace Tommy\Bundle\JsTemplatingBundle\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo as File;
use Tommy\Bundle\JsTemplatingBundle\Configuration\NamespaceMapping;
use Tommy\Bundle\JsTemplatingBundle\Exception\InvalidPathException;

/**
 * @class  DumpProcessor
 * @author Tomfun <tomfun1990@gmail.com>
 */
class DumpProcessor
{
    /** @var NamespaceMapping */
    protected $mapper;
    /** @var  string[] */
    protected $basePaths;
    /** @var  string */
    protected $kernelRoot;
    /** @var  string */
    protected $exportJsonFile;
    /** @var  bool */
    protected $useSymLinks;


    /**
     * @param NamespaceMapping $mapper
     * @param                  $kernelRoot
     * @param                  $symLinks
     * @param array            $basePaths The base path to serve resources
     * @param                  $exportJsonFile
     */
    public function __construct(NamespaceMapping $mapper, $kernelRoot, $symLinks, $basePaths, $exportJsonFile)
    {
        $this->mapper = $mapper;
        $this->basePaths = $basePaths;
        $this->kernelRoot = $kernelRoot;
        $this->useSymLinks = !!$symLinks;
        $this->exportJsonFile = $exportJsonFile;
    }

    public function dump()
    {
        $data = $this->mapper->getRegisteredPaths();
        foreach ($data as $type => $items) {
            $files = $this->collectFiles($type, $items);
            $this->processDump($this->basePaths[$type], $files);
        }
    }

    /**
     * @param null $bundle not implemented
     * @param null $type   not implemented
     * @throws \Exception
     */
    public function exportJsonFile($bundle = null, $type = null, $files = false)
    {
        $json = $this->buildJson($bundle, $type, $files);
        $info = new \SplFileInfo($this->kernelRoot . '/../' . trim($this->exportJsonFile, '\/'));
        $path = $info->getPath();
        if (!is_dir($path)) {
            if (!mkdir($path, 0770, true)) {
                throw new InvalidPathException('Can\'t create dir: ' . $path);
            }
        }
        if (!file_put_contents($info->getPathname(), $json)) {
            throw new \Exception('File is not writable: ' . $this->exportJsonFile);
        }
    }

    /**
     * @param null $bundle not implemented
     * @param null $type   not implemented
     * @return string
     */
    public function buildJson($bundle = null, $type = null, $files = true)
    {
        $data = $this->mapper->getRegisteredPaths();
        $groups = [];
        foreach ($data as $type => $items) {
            $groups[$type] = $this->collectFiles($type, $items, $files);
        }
        return json_encode($this->processJson($groups));
    }

    /**
     * @param string $type
     * @param array  $items
     * @return array
     */
    public function collectFiles($type, $items, $files = true)
    {
        $res = [];
        foreach ($items as $item) {
            $res[$item['exportName']] = [];
        }
        foreach ($items as $item) {
            $path = $item['path'];
            $exportName = $item['exportName'];
            if (!$files) {
                $res[$exportName][] = [
                    'prefix'           => $path,
                    'realPath'         => $path . '/**/*.' . $type,
                    'relativePathName' => '',
                    'relativePath'     => '',
                    'baseName'         => basename($path),
                ];
                continue;
            }
            if (is_file($path)) {
                if ((($temp = strlen($path) - strlen($type)) >= 0 && strpos($path, $type, $temp) !== false)) {
                    // TODO: NOT WORKING
                }
            } else {
                $finder = new Finder();
                $finder->followLinks()->name('*.' . $type)->files()->in($path);
                foreach ($finder as $file) {
                    /** @var File $file */
                    $res[$exportName][] = [
                        'prefix'           => $path,
                        'realPath'         => $file->getRealPath(),
                        'relativePathName' => $file->getRelativePathname(),
                        'relativePath'     => $file->getRelativePath(),
                        'baseName'         => $file->getBasename(),
                    ];
                    var_dump($res[$exportName]);
                    die();
                }
            }
        }
        return $res;
    }

    /**
     * @param string $destination
     * @param array  $group
     */
    protected function processDump($destination, $group)
    {
        $destination = $this->kernelRoot . '/../' . rtrim($destination, '\/');
        if (is_file($destination)) {
            throw new InvalidPathException('Destination dir is not the dir: ' . realpath($destination));
        }
        if (is_dir($destination) && !is_writable($destination)) {
            throw new InvalidPathException('Destination dir is not writable: ' . realpath($destination));
        }
        foreach ($group as $exportName => $files) {
            foreach ($files as $file) {
                $subDestination = trim($exportName, '\/');
                if ($subDestination === '.' || !$subDestination) {
                    $subDestination = $destination;
                } else {
                    $subDestination = $destination . '/' . $subDestination;
                }
                $subDestination .= '/' . $file['relativePath'];
                if (!is_dir($subDestination)) {
                    if (!mkdir($subDestination, 0770, true)) {
                        throw new InvalidPathException('Can\'t create dir: ' . $subDestination);
                    }
                }
                $normalizedName = $subDestination . '/' . $file['baseName'];
                if ($this->useSymLinks) {
                    if (file_exists($normalizedName)) {
                        unlink($normalizedName);
                    }
                    symlink($file['realPath'], $normalizedName);
                } else {
                    copy($file['realPath'], $normalizedName);
                }
            }
        }
    }

    /**
     * @param array $groups
     * @return array
     */
    protected function processJson($groups)
    {
        $res = [];
        foreach ($groups as $type => $group) {
            $res[$type] = [];
            foreach ($group as $exportName => $files) {
                foreach ($files as $file) {
                    $subDestination = trim($exportName, '\/');
                    if ($subDestination === '.' || !$subDestination) {
                        $subDestination = '.';
                    } else {
                        $subDestination = './' . $subDestination;
                    }
                    if ($file['relativePath']) {
                        $subDestination .= '/' . $file['relativePath'];
                    }
                    $subDestination .= '/' . $file['baseName'];
                    $res[$type][$subDestination] = [
                        'path'   => $file['realPath'],
                        'prefix' => $file['prefix'],
                    ];
                }
            }
        }
        return $res;
    }

    /**
     * @return \string[]
     */
    public function getBasePaths()
    {
        return $this->basePaths;
    }

    /**
     * @return boolean
     */
    public function isUseSymLinks()
    {
        return $this->useSymLinks;
    }

    /**
     * @return string
     */
    public function getExportJsonFile()
    {
        return $this->exportJsonFile;
    }


}