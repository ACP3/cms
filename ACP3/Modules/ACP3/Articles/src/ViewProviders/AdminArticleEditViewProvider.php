<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\ViewProviders;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Environment\Theme;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Articles\Helpers;

class AdminArticleEditViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\Environment\Theme
     */
    private $theme;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        Forms $formsHelper,
        FormToken $formTokenHelper,
        RequestInterface $request,
        Theme $theme,
        Title $title,
        Translator $translator
    ) {
        $this->formsHelper = $formsHelper;
        $this->request = $request;
        $this->title = $title;
        $this->theme = $theme;
        $this->formTokenHelper = $formTokenHelper;
        $this->translator = $translator;
    }

    /**
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function __invoke(array $article): array
    {
        $this->title->setPageTitlePrefix($article['title']);

        return [
            'active' => $this->formsHelper->yesNoCheckboxGenerator('active', $article['active']),
            'form' => array_merge($article, $this->request->getPost()->all()),
            'layouts' => $this->formsHelper->choicesGenerator(
                'layout',
                $this->getAvailableLayouts(),
                $article['layout']
            ),
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'SEO_URI_PATTERN' => Helpers::URL_KEY_PATTERN,
            'SEO_ROUTE_NAME' => !empty($article['id']) ? sprintf(Helpers::URL_KEY_PATTERN, $article['id']) : '',
        ];
    }

    /**
     * @return string[]
     *
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function getAvailableLayouts(): array
    {
        $paths = [
            $this->theme->getDesignPathInternal() . '/*/View/*/layout.tpl',
            $this->theme->getDesignPathInternal() . '/*/View/*/layout.*.tpl',
            $this->theme->getDesignPathInternal() . '/*/View/layout.tpl',
            $this->theme->getDesignPathInternal() . '/*/View/layout.*.tpl',
            $this->theme->getDesignPathInternal() . '/layout.*.tpl',
        ];

        $layouts = [];
        foreach ($paths as $path) {
            $layouts = array_merge($layouts, glob($path));
        }

        $layouts = array_map(function ($value) {
            return str_replace([$this->theme->getDesignPathInternal() . '/', '/View/'], ['', '/'], $value);
        }, $layouts);

        $layouts = array_combine($layouts, $layouts);

        $layouts = array_merge(['' => $this->translator->t('articles', 'default_layout')], $layouts);

        return $layouts;
    }
}
