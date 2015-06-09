<?php
namespace Tommy\Bundle\JsTemplatingBundle\Exception;

/**
 * This class represents the exception that is thrown if a type does not match
 * with the expected type
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 * @codeCoverageIgnore
 */
class InvalidTypeException extends InvalidArgumentException implements ExceptionInterface
{
}
