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
    public function __construct(private readonly RequestInterface $request, private readonly Steps $breadcrumb, private readonly Title $title, private readonly Translator $translator)
    {
    }

    /**
     * @param array<string, mixed> $newsletter
     *
     * @return array<string, mixed>
     */
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
