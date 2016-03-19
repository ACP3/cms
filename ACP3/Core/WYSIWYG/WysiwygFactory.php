<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\WYSIWYG;


class WysiwygFactory
{
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var AbstractWYSIWYG[]
     */
    protected $wysiwygEditors = [];

    /**
     * @param \ACP3\Core\WYSIWYG\AbstractWYSIWYG $wysiwygEditor
     * @param string                             $wysiwygEditorName
     *
     * @return $this
     */
    public function registerWysiwygEditor(AbstractWYSIWYG $wysiwygEditor, $wysiwygEditorName)
    {
        $this->wysiwygEditors[$wysiwygEditorName] = $wysiwygEditor;

        return $this;
    }

    /**
     * @return \ACP3\Core\WYSIWYG\AbstractWYSIWYG[]
     */
    public function getWysiwygEditors()
    {
        return $this->wysiwygEditors;
    }

    /**
     * @param string $wysiwygEditorName
     *
     * @return \ACP3\Core\WYSIWYG\AbstractWYSIWYG
     */
    public function create($wysiwygEditorName)
    {
        if (isset($this->wysiwygEditors[$wysiwygEditorName])) {
            return $this->wysiwygEditors[$wysiwygEditorName];
        }

        throw new \InvalidArgumentException('Can not find the WYSIWYG-Editor with the name: ' . $wysiwygEditorName);
    }
}
