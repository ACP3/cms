<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

class TemplatePath extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Assets\FileResolver
     */
    protected $fileResolver;

    /**
     * @param \ACP3\Core\Assets\FileResolver $fileResolver
     */
    public function __construct(Core\Assets\FileResolver $fileResolver)
    {
        $this->fileResolver = $fileResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionName()
    {
        return 'template_path';
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->fileResolver->resolveTemplatePath($params['path']);
    }
}
