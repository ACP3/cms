<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;


use ACP3\Core\View;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

/**
 * Class OnLayoutMetaListener
 * @package ACP3\Modules\ACP3\Seo\Event\Listener
 */
class OnLayoutMetaListener
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var View
     */
    protected $view;

    /**
     * OnCustomTemplateVariable constructor.
     *
     * @param View $view
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function __construct(
        View $view,
        MetaStatements $metaStatements)
    {
        $this->view = $view;
        $this->metaStatements = $metaStatements;
    }

    public function renderSeoMetaTags()
    {
        $this->view->assign('META', $this->metaStatements->getMetaTags());

        echo $this->view->fetchTemplate('Seo/Partials/meta.tpl');
    }
}
