<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

class NewsletterDetailsViewProvider
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        RequestInterface $request,
        Steps $breadcrumb,
        Title $title,
        Translator $translator
    ) {
        $this->request = $request;
        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
        $this->translator = $translator;
    }

    public function __invoke(array $newsletter): array
    {
        $this->breadcrumb
            ->append($this->translator->t('newsletter', 'index'), 'newsletter')
            ->append($this->translator->t('newsletter', 'frontend_archive_index'), 'newsletter/archive')
            ->append(
                $newsletter['title'],
                $this->request->getQuery()
            );
        $this->title->setPageTitle($newsletter['title']);

        return [
            'newsletter' => $newsletter,
        ];
    }
}
