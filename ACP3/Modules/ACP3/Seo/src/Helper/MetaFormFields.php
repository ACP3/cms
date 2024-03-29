<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Helper;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;

class MetaFormFields
{
    public function __construct(private readonly MetaStatementsServiceInterface $metaStatements, private readonly Translator $translator, private readonly RequestInterface $request, private readonly Aliases $aliases, private readonly Forms $formsHelper)
    {
    }

    /**
     * Returns the SEO form fields.
     *
     * @return array<string, mixed>
     */
    public function formFields(string $path = ''): array
    {
        if (!empty($path)) {
            $path .= !preg_match('/\/$/', $path) ? '/' : '';

            $alias = $this->request->getPost()->get('alias', $this->aliases->getUriAlias($path, true));
            $title = $this->request->getPost()->get('seo_title', $this->metaStatements->getTitle($path));
            $keywords = $this->request->getPost()->get('seo_keywords', $this->metaStatements->getKeywords($path));
            $description = $this->request->getPost()->get(
                'seo_description',
                $this->metaStatements->getDescription($path)
            );
            $canonical = $this->request->getPost()->get(
                'seo_canonical',
                $this->metaStatements->getCanonicalUrl($path)
            );
            $robots = $this->metaStatements->getSeoInformation($path, 'robots', '0');
            $structuredData = $this->request->getPost()->get('seo_structured_data', $this->metaStatements->getStructuredData($path));
        } else {
            $alias = $title = $keywords = $description = $structuredData = $canonical = '';
            $robots = 0;
        }

        return [
            'title' => $title,
            'alias' => $alias,
            'keywords' => $keywords,
            'description' => $description,
            'canonical' => $canonical,
            'structured_data' => $structuredData,
            'robots' => $this->formsHelper->choicesGenerator(
                'seo_robots',
                $this->getRobotsChoicesGeneratorValues(),
                $robots
            ),
        ];
    }

    /**
     * @return string[]
     */
    protected function getRobotsChoicesGeneratorValues(): array
    {
        return [
            0 => $this->translator->t(
                'seo',
                'robots_use_system_default',
                ['%default%' => $this->metaStatements->getRobotsSetting()]
            ),
            1 => $this->translator->t('seo', 'robots_index_follow'),
            2 => $this->translator->t('seo', 'robots_index_nofollow'),
            3 => $this->translator->t('seo', 'robots_noindex_follow'),
            4 => $this->translator->t('seo', 'robots_noindex_nofollow'),
        ];
    }
}
