<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Seo\Controller\Admin\Index
 */
class Edit extends Core\Controller\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Cache
     */
    protected $seoCache;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\SeoRepository
     */
    protected $seoRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\AdminFormValidation
     */
    protected $adminFormValidation;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext            $context
     * @param \ACP3\Core\Helpers\FormToken                          $formTokenHelper
     * @param \ACP3\Modules\ACP3\Seo\Cache                          $seoCache
     * @param \ACP3\Modules\ACP3\Seo\Model\SeoRepository            $seoRepository
     * @param \ACP3\Modules\ACP3\Seo\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Seo\Cache $seoCache,
        Seo\Model\SeoRepository $seoRepository,
        Seo\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->seoCache = $seoCache;
        $this->seoRepository = $seoRepository;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $seo = $this->seoRepository->getOneById($id);

        if (empty($seo) === false) {
            $this->breadcrumb->setTitlePostfix($seo['alias']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $seo['uri'], $id);
            }

            return [
                'SEO_FORM_FIELDS' => $this->seo->formFields($seo['uri']),
                'form' => array_merge(['uri' => $seo['uri']], $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array  $formData
     * @param string $path
     * @param int    $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $path, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $path, $id) {
            $this->adminFormValidation
                ->setUriAlias($path)
                ->validate($formData);

            $updateValues = [
                'uri' => $formData['uri'],
                'alias' => $formData['alias'],
                'keywords' => Core\Functions::strEncode($formData['seo_keywords']),
                'description' => Core\Functions::strEncode($formData['seo_description']),
                'robots' => (int)$formData['seo_robots']
            ];

            $bool = $this->seoRepository->update($updateValues, $id);

            $this->seoCache->saveCache();

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}