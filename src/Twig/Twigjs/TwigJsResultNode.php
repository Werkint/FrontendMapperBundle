<?php
namespace Tommy\Bundle\JsTemplatingBundle\Twig\Twigjs;

/**
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class TwigJsResultNode extends \Twig_Node
{
    /**
     * @param int    $lineno
     * @param string $tag
     */
    public function __construct($lineno = 0, $tag = null)
    {
        parent::__construct([], [], $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $ext = JsTemplatingExtension::EXT_NAME;
        $compiler
            ->addDebugInfo($this)
            ->write('echo $this->env->getExtension(\'' . $ext . '\')->loadModules()')
            ->raw(";\n");
    }
}