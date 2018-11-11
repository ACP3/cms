<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core\Controller\Context\FormContext;
use ACP3\Modules\ACP3\Articles\Model\ArticlesModel;

class Duplicate extends \ACP3\Core\Controller\AbstractFormAction
{
    /**
     * @var ArticlesModel
     */
    private $articlesModel;

    public function __construct(
        FormContext $context,
        ArticlesModel $articlesModel
    ) {
        parent::__construct($context);

        $this->articlesModel = $articlesModel;
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        $result = $this->articlesModel->duplicate($id);

        return $this->redirectMessages()->setMessage(
            $result,
            $this->translator->t('system', $result !== false ? 'duplicate_success' : 'duplicate_error')
        );
    }
}
