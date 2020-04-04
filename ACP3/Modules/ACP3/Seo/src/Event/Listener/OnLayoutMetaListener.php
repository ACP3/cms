<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;

use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Core\View;

class OnLayoutMetaListener
{
    /**
     * @var \ACP3\Core\SEO\MetaStatementsServiceInterface
     */
    protected $metaStatements;
    /**
     * @var View
     */
    protected $view;

    public function __construct(
        View $view,
        MetaStatementsServiceInterface $metaStatements
    ) {
        $this->view = $view;
        $this->metaStatements = $metaStatements;
    }

    public function __invoke()
    {
        $this->view->assign('META', $this->metaStatements->getMetaTags());

        $this->view->displayTemplate('Seo/Partials/meta.tpl');
    }
}
