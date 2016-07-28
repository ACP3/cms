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
     * @var \ACP3\Core\Helpers\Formatter\RewriteInternalUri
     */
    protected $rewriteInternalUri;

    /**
     * @param \ACP3\Core\Helpers\Formatter\RewriteInternalUri $rewriteInternalUri
     */
    public function __construct(RewriteInternalUri $rewriteInternalUri)
    {
        $this->rewriteInternalUri = $rewriteInternalUri;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
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
