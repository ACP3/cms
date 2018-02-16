<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;


use ACP3\Core\Controller\AbstractFrontendAction;

abstract class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @return string[]
     */
    protected function getAvailableLayouts(): array
    {
        $paths = [
            $this->appPath->getDesignPathInternal() . '*/View/*/layout.tpl',
            $this->appPath->getDesignPathInternal() . '*/View/*/layout.*.tpl',
            $this->appPath->getDesignPathInternal() . '*/View/layout.tpl',
            $this->appPath->getDesignPathInternal() . '*/View/layout.*.tpl',
            $this->appPath->getDesignPathInternal() . 'layout.*.tpl',
        ];

        $layouts = [];
        foreach ($paths as $path) {
            $layouts = array_merge($layouts, glob($path));
        }

        $layouts = array_map(function ($value) {
            return str_replace([$this->appPath->getDesignPathInternal(), '/View/'], ['', '/'], $value);
        }, $layouts);

        $layouts = array_combine($layouts, $layouts);

        $layouts = ['' => $this->translator->t('articles', 'default_layout')] + $layouts;

        return $layouts;
    }
}
