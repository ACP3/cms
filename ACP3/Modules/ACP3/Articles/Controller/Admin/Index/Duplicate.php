<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Modules\ACP3\Articles\Model\ArticlesModel;

class Duplicate extends AbstractFrontendAction
{
    /**
     * @var ArticlesModel
     */
    private $articlesModel;

    /**
     * Duplicate constructor.
     * @param FrontendContext $context
     * @param ArticlesModel $articlesModel
     */
    public function __construct(
        FrontendContext $context,
        ArticlesModel $articlesModel
    ) {
        parent::__construct($context);

        $this->articlesModel = $articlesModel;
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($id)
    {
        $result = $this->articlesModel->duplicate($id);

        return $this->redirectMessages()->setMessage(
            $result,
            $this->translator->t('system', $result !== false ? 'duplicate_success' : 'duplicate_error')
        );
    }
}
