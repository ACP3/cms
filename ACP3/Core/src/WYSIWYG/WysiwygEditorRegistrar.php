<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\WYSIWYG;

use ACP3\Core\WYSIWYG\Editor\AbstractWYSIWYG;

class WysiwygEditorRegistrar
{
    /**
     * @var AbstractWYSIWYG[]
     */
    protected $wysiwygEditors = [];

    /**
     * @param string $serviceId
     *
     * @return $this
     */
    public function registerWysiwygEditor($serviceId, AbstractWYSIWYG $wysiwygEditor)
    {
        $this->wysiwygEditors[$serviceId] = $wysiwygEditor;

        return $this;
    }

    /**
     * @return \ACP3\Core\WYSIWYG\Editor\AbstractWYSIWYG[]
     */
    public function all(): array
    {
        return $this->wysiwygEditors;
    }

    public function has(string $serviceId): bool
    {
        return isset($this->wysiwygEditors[$serviceId]);
    }

    public function get(string $serviceId): AbstractWYSIWYG
    {
        if ($this->has($serviceId)) {
            return $this->wysiwygEditors[$serviceId];
        }

        throw new \InvalidArgumentException(\sprintf('Can not find the WYSIWYG-Editor with the name: %s', $serviceId));
    }
}
