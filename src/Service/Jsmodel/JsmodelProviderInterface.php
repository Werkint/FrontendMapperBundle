<?php
namespace Werkint\Bundle\FrontendMapperBundle\Service\Jsmodel;

/**
 * Интерфейс класса для предоставления папок
 *
 * @deprecated
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
interface JsmodelProviderInterface
{
    /**
     * @return array|string[]
     */
    public function getPaths();
} 