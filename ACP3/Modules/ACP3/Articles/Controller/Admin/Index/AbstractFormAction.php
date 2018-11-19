<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core\Controller\Context\FormContext;
use ACP3\Core\Environment\ThemePathInterface;

abstract class AbstractFormAction extends \ACP3\Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Core\Environment\ThemePathInterface
     */
    private $theme;

    public function __construct(FormContext $context, ThemePathInterface $theme)
    {
        parent::__construct($context);

        $this->theme = $theme;
    }

    /**
     * @return string[]
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function getAvailableLayouts(): array
    {
        $paths = [
            $this->theme->getDesignPathInternal() . '*/View/*/layout.tpl',
            $this->theme->getDesignPathInternal() . '*/View/*/layout.*.tpl',
            $this->theme->getDesignPathInternal() . '*/View/layout.tpl',
            $this->theme->getDesignPathInternal() . '*/View/layout.*.tpl',
            $this->theme->getDesignPathInternal() . 'layout.*.tpl',
        ];

        $layouts = [];
        foreach ($paths as $path) {
            $layouts = \array_merge($layouts, \glob($path));
        }

        $layouts = \array_map(function ($value) {
            return \str_replace([$this->theme->getDesignPathInternal(), '/View/'], ['', '/'], $value);
        }, $layouts);

        $layouts = \array_combine($layouts, $layouts);

        $layouts = \array_merge(['' => $this->translator->t('articles', 'default_layout')], $layouts);

        return $layouts;
    }
}
