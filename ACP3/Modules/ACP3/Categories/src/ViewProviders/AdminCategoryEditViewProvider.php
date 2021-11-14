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
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;

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
     * @var \ACP3\Modules\ACP3\Categories\Repository\CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        Forms $formsHelper,
        FormToken $formTokenHelper,
        Modules $modules,
        RequestInterface $request,
        Title $title,
        Translator $translator,
        CategoryRepository $categoryRepository
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->title = $title;
        $this->categoryRepository = $categoryRepository;
        $this->modules = $modules;
        $this->translator = $translator;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(array $category): array
    {
        $this->title->setPageTitlePrefix($category['title']);

        return [
            'form' => array_merge($category, $this->request->getPost()->all()),
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
        $modules = [];
        foreach ($this->modules->getInstalledModules() as $info) {
            if (\in_array('categories', $info['dependencies'], true) === true) {
                $modules[(int) $info['id']] = $this->translator->t($info['name'], $info['name']);
            }
        }

        uasort($modules, static function ($a, $b) {
            return $a <=> $b;
        });

        return $this->formsHelper->choicesGenerator('module_id', $modules);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function fetchCategoryTree(
        ?int $moduleId = null,
        ?int $parentId = null,
        ?int $leftId = null,
        ?int $rightId = null
    ): array {
        if ($moduleId === null) {
            return [];
        }

        $categories = [];
        foreach ($this->categoryRepository->getAllByModuleId($moduleId) as $category) {
            if ($category['left_id'] >= $leftId && $category['right_id'] <= $rightId) {
                continue;
            }

            $categories[(int) $category['id']] = str_repeat('&nbsp;&nbsp;', $category['level']) . $category['title'];
        }

        return $this->formsHelper->choicesGenerator('parent_id', $categories, $parentId);
    }
}
