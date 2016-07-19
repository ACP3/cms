<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
     */
    protected $articleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var Menus\Helpers\MenuItemFormFields
     */
    protected $menuItemFormFieldsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields
     */
    protected $metaFormFieldsHelper;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext                 $context
     * @param \ACP3\Core\Date                                            $date
     * @param \ACP3\Core\Helpers\Forms                                   $formsHelper
     * @param \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository        $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Core\Helpers\FormToken                               $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Articles\Model\Repository\ArticleRepository $articleRepository,
        Articles\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\FormToken $formTokenHelper
    ) {
        parent::__construct($context, $formsHelper);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields $metaFormFieldsHelper
     */
    public function setMetaFormFieldsHelper(MetaFormFields $metaFormFieldsHelper)
    {
        $this->metaFormFieldsHelper = $metaFormFieldsHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields $menuItemFormFieldsHelper
     *
     * @return $this
     */
    public function setMenuItemFormFieldsHelper(Menus\Helpers\MenuItemFormFields $menuItemFormFieldsHelper)
    {
        $this->menuItemFormFieldsHelper = $menuItemFormFieldsHelper;

        return $this;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all());
        }



        $defaults = [
            'title' => '',
            'text' => '',
            'start' => '',
            'end' => ''
        ];

        return [
            'options' => $this->fetchOptions(),
            'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper ? $this->metaFormFieldsHelper->formFields() : [],
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->adminFormValidation->validate($formData);

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => $this->get('core.helpers.secure')->strEncode($formData['title']),
                'text' => $this->get('core.helpers.secure')->strEncode($formData['text'], true),
                'user_id' => $this->user->getUserId(),
            ];

            $articleId = $this->articleRepository->insert($insertValues);

            $this->insertUriAlias($formData, $articleId);

            $this->createOrUpdateMenuItem($formData, $articleId);

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $articleId;
        });
    }

    /**
     * @return array
     */
    protected function fetchOptions()
    {
        $options = [];
        if ($this->acl->hasPermission('admin/menus/items/create') === true) {
            $options = $this->fetchCreateMenuItemOption(0);

            $this->view->assign($this->menuItemFormFieldsHelper->createMenuItemFormFields());
        }

        return $options;
    }
}
