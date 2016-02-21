<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
class Edit extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\ArticleRepository
     */
    protected $articleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    protected $articlesCache;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var Menus\Helpers\MenuItemFormFields
     */
    protected $menuItemFormFieldsHelper;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext                 $context
     * @param \ACP3\Core\Date                                            $date
     * @param \ACP3\Modules\ACP3\Articles\Model\ArticleRepository        $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache                          $articlesCache
     * @param \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Core\Helpers\FormToken                               $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Articles\Model\ArticleRepository $articleRepository,
        Articles\Cache $articlesCache,
        Articles\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
        $this->articlesCache = $articlesCache;
        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
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
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository $menuItemRepository
     *
     * @return $this
     */
    public function setMenuItemRepository(Menus\Model\MenuItemRepository $menuItemRepository)
    {
        $this->menuItemRepository = $menuItemRepository;

        return $this;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $article = $this->articleRepository->getOneById($id);

        if (empty($article) === false) {
            $this->breadcrumb->setTitlePostfix($article['title']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            if ($this->acl->hasPermission('admin/menus/items/create') === true) {
                $menuItem = $this->menuItemRepository->getOneMenuItemByUri(sprintf(Articles\Helpers::URL_KEY_PATTERN, $id));

                $langOptions = [$this->translator->t('articles', 'create_menu_item')];
                $this->view->assign('options', $this->get('core.helpers.forms')->checkboxGenerator('create', [1], $langOptions, !empty($menuItem) ? 1 : 0));

                $this->view->assign(
                    $this->menuItemFormFieldsHelper->createMenuItemFormFields(
                        $menuItem['block_id'],
                        $menuItem['parent_id'],
                        $menuItem['left_id'],
                        $menuItem['right_id'],
                        $menuItem['display']
                    )
                );
            }

            return [
                'SEO_FORM_FIELDS' => $this->seo->formFields(sprintf(Articles\Helpers::URL_KEY_PATTERN, $id)),
                'form' => array_merge($article, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $id) {
            $this->adminFormValidation
                ->setUriAlias(sprintf(Articles\Helpers::URL_KEY_PATTERN, $id))
                ->validate($formData);

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->user->getUserId(),
            ];

            $bool = $this->articleRepository->update($updateValues, $id);

            $this->seo->insertUriAlias(
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->articlesCache->saveCache($id);

            $this->createOrUpdateMenuItem($formData, $id);

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
