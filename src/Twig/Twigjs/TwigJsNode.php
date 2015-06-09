<?php
namespace Tommy\Bundle\JsTemplatingBundle\Twig\Twigjs;

/**
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class TwigJsNode extends \Twig_Node
{
    /**
     * @param \Twig_Node_Expression $name
     * @param int                   $lineno
     * @param null                  $tag
     */
    public function __construct(
        \Twig_Node_Expression $name,
        $lineno = 0,
        $tag = null
    ) {
        parent::__construct(['value' => $name], [], $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $ext = JsTemplatingExtension::EXT_NAME;
        $compiler
            ->addDebugInfo($this)
            ->write('$this->env->getExtension(\'' . $ext . '\')->loadTwigJs(')
            ->subcompile($this->getNode('value'))
            ->write(')')
            ->raw(";\n");
    }
}