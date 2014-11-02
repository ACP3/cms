<?php
namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

use ACP3\Core\Helpers\Formatter\RewriteInternalUri;

/**
 * Class RewriteUri
 * @package ACP3\Core\View\Renderer\Smarty\Modifiers
 */
class RewriteUri extends AbstractModifier
{
    /**
     * @var string
     */
    protected $modifierName = 'rewrite_uri';
    /**
     * @var RewriteInternalUri
     */
    protected $rewriteInternalUri;

    /**
     * @param RewriteInternalUri $rewriteInternalUri
     */
    public function __construct(RewriteInternalUri $rewriteInternalUri)
    {
        $this->rewriteInternalUri = $rewriteInternalUri;
    }

    /**
     * @param $value
     * @return string
     */
    public function process($value)
    {
        return $this->rewriteInternalUri->rewriteInternalUri($value);
    }
}