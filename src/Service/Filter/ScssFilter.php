<?php
namespace Tommy\Bundle\JsTemplatingBundle\Service\Filter;

/**
 * Прокси-класс для смены пути к гемам
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class ScssFilter extends SassFilter
{
    public function __construct()
    {
        call_user_func_array([$this, 'parent::__construct'], func_get_args());

        $this->setScss(true);
    }
}
