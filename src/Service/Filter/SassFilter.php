<?php
namespace Tommy\Bundle\JsTemplatingBundle\Service\Filter;

use Assetic\Filter\Sass\SassFilter as BaseSassFilter;

/**
 * Прокси-класс для смены пути к гемам
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class SassFilter extends BaseSassFilter
{
    protected $gemPath;
    protected $debug;
    protected $encoding;

    public function __construct(
        $gemPath,
        $rubyPath = null,
        $debug = null,
        $encoding = null
    ) {
        $this->gemPath = $gemPath;
        $this->debug = $debug;
        $this->encoding = $encoding;
        parent::__construct($gemPath . '/bin/sass', $rubyPath);
    }

    protected $loadPaths = [];

    public function addLoadPath($path)
    {
        $this->loadPaths[] = $path;
    }

    /**
     * {@inheritdoc}
     */
    protected function createProcessBuilder(array $arguments = [])
    {
        $pb = parent::createProcessBuilder($arguments);

        // TODO: ENV вообще
        if ($this->gemPath) {
            $pb->setEnv('GEM_PATH', $this->gemPath);
        }

        foreach ($this->loadPaths as $path) {
            $pb->add('--load-path')->add($path);
        }

        if ($this->debug) {
            // TODO: restore
            //$pb->add('--sourcemap');
            $pb->add('--trace');
        }
        if ($this->encoding) {
            $pb->add('--default-encoding')->add($this->encoding);
        }

        return $pb;
    }
}
