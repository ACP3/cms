<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Helper;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;

class MetaFormFields
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Core\Router\Aliases
     */
    protected $aliases;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * MetaFormFields constructor.
     *
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function __construct(
        MetaStatements $metaStatements,
        Translator $translator,
        RequestInterface $request,
        Aliases $aliases,
        Forms $formsHelper
    ) {
        $this->metaStatements = $metaStatements;
        $this->translator = $translator;
        $this->request = $request;
        $this->aliases = $aliases;
        $this->formsHelper = $formsHelper;
    }

    /**
     * Returns the SEO form fields.
     */
    public function formFields(string $path = ''): array
    {
        if (!empty($path)) {
            $path .= !\preg_match('/\/$/', $path) ? '/' : '';

            $alias = $this->request->getPost()->get('alias', $this->aliases->getUriAlias($path, true));
            $title = $this->request->getPost()->get('seo_title', $this->metaStatements->getTitle($path));
            $keywords = $this->request->getPost()->get('seo_keywords', $this->metaStatements->getKeywords($path));
            $description = $this->request->getPost()->get(
                'seo_description',
                $this->metaStatements->getDescription($path)
            );
            $robots = $this->metaStatements->getSeoInformation($path, 'robots', 0);
        } else {
            $alias = $title = $keywords = $description = '';
            $robots = 0;
        }

        return [
            'title' => $title,
            'alias' => $alias,
            'keywords' => $keywords,
            'description' => $description,
            'robots' => $this->formsHelper->choicesGenerator(
                'seo_robots',
                $this->getRobotsChoicesGeneratorValues(),
                $robots
            ),
        ];
    }

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
