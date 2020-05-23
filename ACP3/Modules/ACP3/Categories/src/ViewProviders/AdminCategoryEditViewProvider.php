<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\ViewProviders;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;

class AdminCategoryEditViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(
        Forms $formsHelper,
        FormToken $formTokenHelper,
        Modules $modules,
        RequestInterface $request,
        Title $title,
        CategoryRepository $categoryRepository
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->title = $title;
        $this->categoryRepository = $categoryRepository;
        $this->modules = $modules;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(array $category): array
    {
        $this->title->setPageTitlePrefix($category['title']);

        return [
            'form' => \array_merge($category, $this->request->getPost()->all()),
            'category_tree' => $this->fetchCategoryTree(
                $category['module_id'] ?? null,
                $category['parent_id'] ?? null,
                $category['left_id'] ?? null,
                $category['right_id'] ?? null
            ),
            'mod_list' => empty($category['id']) ? $this->fetchModules() : [],
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    private function fetchModules(): array
    {
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $name => $info) {
            if ($info['active'] && \in_array('categories', $info['dependencies'], true) === true) {
                $modules[$name]['selected'] = $this->formsHelper->selectEntry('module_id', $info['id']);
            } else {
                unset($modules[$name]);
            }
        }

        return $modules;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function fetchCategoryTree(
        ?int $moduleId = null,
        ?int $parentId = null,
        ?int $leftId = null,
        ?int $rightId = null
    ): array {
        $categories = [];
        if ($moduleId !== null) {
            $categories = $this->categoryRepository->getAllByModuleId($moduleId);
            foreach ($categories as $i => $category) {
                if ($category['left_id'] >= $leftId && $category['right_id'] <= $rightId) {
                    unset($categories[$i]);
                } else {
                    $categories[$i]['selected'] = $this->formsHelper->selectEntry('parent_id', $category['id'], $parentId);
                    $categories[$i]['title'] = \str_repeat('&nbsp;&nbsp;', $category['level']) . $category['title'];
                }
            }
        }

        return $categories;
    }
}
