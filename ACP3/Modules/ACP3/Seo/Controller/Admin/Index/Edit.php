<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var Seo\Model\SeoModel
     */
    protected $seoModel;
    /**
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface $block
     * @param Seo\Model\SeoModel $seoModel
     * @param \ACP3\Modules\ACP3\Seo\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Seo\Model\SeoModel $seoModel,
        Seo\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->seoModel = $seoModel;
        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function execute(int $id)
    {
        return $this->block
            ->setDataById($id)
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param int    $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $seo = $this->seoModel->getOneById($id);

            $this->adminFormValidation
                ->setUriAlias($seo['uri'])
                ->validate($formData);

            return $this->seoModel->save($formData, $id);
        });
    }
}
