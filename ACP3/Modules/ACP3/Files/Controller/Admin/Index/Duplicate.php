<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core\Controller\Context\FormContext;
use ACP3\Modules\ACP3\Files\Model\FilesModel;

class Duplicate extends \ACP3\Core\Controller\AbstractFormAction
{
    /**
     * @var FilesModel
     */
    private $filesModel;

    public function __construct(
        FormContext $context,
        FilesModel $filesModel
    ) {
        parent::__construct($context);

        $this->filesModel = $filesModel;
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
        $result = $this->filesModel->duplicate($id);

        return $this->redirectMessages()->setMessage(
            $result,
            $this->translator->t('system', $result !== false ? 'duplicate_success' : 'duplicate_error')
        );
    }
}
