<?php
namespace Tommy\Bundle\JsTemplatingBundle\Exception;

/**
 * This class represents the exception that is thrown if an error which can only
 * be found on runtime occurs
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 * @codeCoverageIgnore
 */
class RuntimeException extends \RuntimeException implements ExceptionInterface
{
}
