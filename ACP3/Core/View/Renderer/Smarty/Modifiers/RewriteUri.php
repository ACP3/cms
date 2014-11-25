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
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'rewrite_uri';
    }

    /**
     * @inheritdoc
     */
    public function process($value)
    {
        return $this->rewriteInternalUri->rewriteInternalUri($value);
    }
}
