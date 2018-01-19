<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Modules\ACP3\News\Model\NewsModel;

class Duplicate extends AbstractFrontendAction
{
    /**
     * @var NewsModel
     */
    private $newsModel;

    /**
     * Duplicate constructor.
     *
     * @param FrontendContext $context
     * @param NewsModel       $newsModel
     */
    public function __construct(
        FrontendContext $context,
        NewsModel $newsModel
    ) {
        parent::__construct($context);

        $this->newsModel = $newsModel;
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($id)
    {
        $result = $this->newsModel->duplicate($id);

        return $this->redirectMessages()->setMessage(
            $result,
            $this->translator->t('system', $result !== false ? 'duplicate_success' : 'duplicate_error')
        );
    }
}
