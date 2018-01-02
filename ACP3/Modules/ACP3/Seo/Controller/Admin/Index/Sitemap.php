<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Model\SitemapGenerationModel;

class Sitemap extends AbstractFrontendAction
{
    /**
     * @var SitemapGenerationModel
     */
    protected $sitemapGenerationModel;

    /**
     * Sitemap constructor.
     * @param FrontendContext $context
     * @param SitemapGenerationModel $sitemapGenerationModel
     */
    public function __construct(
        FrontendContext $context,
        SitemapGenerationModel $sitemapGenerationModel
    ) {
        parent::__construct($context);

        $this->sitemapGenerationModel = $sitemapGenerationModel;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $result = false;
        $phrase = 'sitemap_error';
        if ($this->config->getSettings(Schema::MODULE_NAME)['sitemap_is_enabled'] == 1) {
            $result = $this->sitemapGenerationModel->save();
            $phrase = $result === true ? 'sitemap_success' : 'sitemap_error';
        }

        return $this->redirectMessages()->setMessage($result, $this->translator->t('seo', $phrase));
    }
}
