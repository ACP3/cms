<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Index
 */
class Edit extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository
     */
    protected $menuRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Validation\MenuFormValidation
     */
    protected $menuFormValidation;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext             $context
     * @param \ACP3\Core\Helpers\FormToken                           $formTokenHelper
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository          $menuRepository
     * @param \ACP3\Modules\ACP3\Menus\Cache                         $menusCache
     * @param \ACP3\Modules\ACP3\Menus\Validation\MenuFormValidation $menuFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Menus\Model\Repository\MenuRepository $menuRepository,
        Menus\Cache $menusCache,
        Menus\Validation\MenuFormValidation $menuFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->menuRepository = $menuRepository;
        $this->menusCache = $menusCache;
        $this->menuFormValidation = $menuFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $menu = $this->menuRepository->getOneById($id);

        if (empty($menu) === false) {
            $this->title->setPageTitlePostfix($menu['title']);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            return [
                'form' => array_merge($menu, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
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
            $this->menuFormValidation
                ->setMenuId($id)
                ->validate($formData);

            $updateValues = [
                'index_name' => $formData['index_name'],
                'title' => $this->get('core.helpers.secure')->strEncode($formData['title']),
            ];

            $bool = $this->menuRepository->update($updateValues, $id);

            $this->menusCache->saveMenusCache();

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $bool;
        });
    }
}
