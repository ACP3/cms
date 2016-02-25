<?php
namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets;
use ACP3\Core\Http\RequestInterface;

/**
 * Class MoveToBottom
 * @package ACP3\Core\View\Renderer\Smarty\Filters
 */
class MoveToBottom extends AbstractFilter
{
    /**
     * @var \ACP3\Core\Assets\AbstractMinifier
     */
    protected $minifier;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;

    /**
     * @param \ACP3\Core\Assets\MinifierInterface $minifier
     * @param \ACP3\Core\Http\RequestInterface    $request
     */
    public function __construct(
        Assets\MinifierInterface $minifier,
        RequestInterface $request
    ) {
        $this->minifier = $minifier;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'output';
    }

    /**
     * @inheritdoc
     */
    public function process($tplOutput, \Smarty_Internal_Template $smarty)
    {
        $pattern = '!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is';

        if (strpos($tplOutput, '<!-- JAVASCRIPTS -->') !== false) {
            $matches = [];
            preg_match_all($pattern, $tplOutput, $matches);

            // Remove placeholder comments
            $tplOutput = preg_replace($pattern, '', $tplOutput);

            $minifyJs = '';
            if (!$this->request->isAjax()) {
                $minifyJs = '<script type="text/javascript" src="' . $this->minifier->getURI() . '"></script>' . "\n";
            }

            return str_replace('<!-- JAVASCRIPTS -->', $minifyJs . implode("\n", array_unique($matches[1])) . "\n", $tplOutput);
        }

        return $tplOutput;
    }
}
