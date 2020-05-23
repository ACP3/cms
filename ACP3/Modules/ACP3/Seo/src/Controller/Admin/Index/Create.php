<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var Seo\Model\SeoModel
     */
    private $seoModel;
    /**
     * @var \ACP3\Modules\ACP3\Seo\ViewProviders\AdminSeoEditViewProvider
     */
    private $adminSeoEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Seo\Model\SeoModel $seoModel,
        Seo\Validation\AdminFormValidation $adminFormValidation,
        Seo\ViewProviders\AdminSeoEditViewProvider $adminSeoEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->seoModel = $seoModel;
        $this->adminSeoEditViewProvider = $adminSeoEditViewProvider;
    }

    public function execute(): array
    {
        $defaults = [
            'alias' => '',
            'uri' => '',
        ];

        return ($this->adminSeoEditViewProvider)($defaults);
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminFormValidation->validate($formData);

            return $this->seoModel->save($formData);
        });
    }
}
