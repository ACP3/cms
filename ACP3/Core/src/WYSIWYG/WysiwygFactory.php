<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\WYSIWYG;

use ACP3\Core\WYSIWYG\Editor\AbstractWYSIWYG;
use Psr\Container\ContainerInterface;

class WysiwygFactory
{
    public function __construct(private readonly ContainerInterface $editorLocator)
    {
    }

    public function create(string $wysiwygEditorName): AbstractWYSIWYG
    {
        return $this->editorLocator->get($wysiwygEditorName);
    }
}
