<?php
namespace Werkint\Bundle\FrontendMapperBundle\Service\Jsmodel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Утилитный класс для сборки файлов
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class Jsmodel
{
    /**
     * Contructor;
     */
    public function __construct()
    {
        $this->providers = new ArrayCollection();
    }

    /**
     * @return Collection|string[]
     */
    public function getPaths()
    {
        $ret = [];

        foreach ($this->providers as $provider) {
            $ret = array_merge($ret, $provider->getPaths());
        }

        return new ArrayCollection($ret);
    }

    // -- Providers ---------------------------------------

    /** @var Collection|JsmodelProviderInterface[] */
    protected $providers;

    /**
     * @param JsmodelProviderInterface $provider
     * @return $this
     */
    public function addProvider(JsmodelProviderInterface $provider)
    {
        $this->providers[] = $provider;
        return $this;
    }
} 