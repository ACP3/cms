<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\WYSIWYG;

class WysiwygFactory
{
    /**
     * @var WysiwygEditorRegistrar
     */
    private $editorRegistrar;

    /**
     * WysiwygFactory constructor.
     */
    public function __construct(WysiwygEditorRegistrar $editorRegistrar)
    {
        $this->editorRegistrar = $editorRegistrar;
    }

    /**
     * @return \ACP3\Core\WYSIWYG\Editor\AbstractWYSIWYG[]
     */
    public function getWysiwygEditors()
    {
        return $this->editorRegistrar->all();
    }

    /**
     * @param string $wysiwygEditorName
     *
     * @return \ACP3\Core\WYSIWYG\Editor\AbstractWYSIWYG
     */
    public function create($wysiwygEditorName)
    {
        return $this->editorRegistrar->get($wysiwygEditorName);
    }
}
