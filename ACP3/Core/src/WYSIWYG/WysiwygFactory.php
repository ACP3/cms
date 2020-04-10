<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\WYSIWYG;

use ACP3\Core\WYSIWYG\Editor\AbstractWYSIWYG;

class WysiwygFactory
{
    /**
     * @var WysiwygEditorRegistrar
     */
    private $editorRegistrar;

    public function __construct(WysiwygEditorRegistrar $editorRegistrar)
    {
        $this->editorRegistrar = $editorRegistrar;
    }

    public function create(string $wysiwygEditorName): AbstractWYSIWYG
    {
        return $this->editorRegistrar->get($wysiwygEditorName);
    }
}
