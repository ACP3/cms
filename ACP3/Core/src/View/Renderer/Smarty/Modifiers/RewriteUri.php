<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

use ACP3\Core\Helpers\Formatter\RewriteInternalUri;

class RewriteUri extends AbstractModifier
{
    /**
     * @var \ACP3\Core\Helpers\Formatter\RewriteInternalUri
     */
    protected $rewriteInternalUri;

    public function __construct(RewriteInternalUri $rewriteInternalUri)
    {
        $this->rewriteInternalUri = $rewriteInternalUri;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionName()
    {
        return 'rewrite_uri';
    }

    /**
     * {@inheritdoc}
     */
    public function process($value)
    {
        return $this->rewriteInternalUri->rewriteInternalUri($value);
    }
}
