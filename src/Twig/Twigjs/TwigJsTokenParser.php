<?php
namespace Tommy\Bundle\JsTemplatingBundle\Twig\Twigjs;

/**
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class TwigJsTokenParser extends \Twig_TokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(\Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $name = $parser->getExpressionParser()->parseExpression();
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new TwigJsNode($name, $token->getLine(), $this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'twigjs';
    }
}