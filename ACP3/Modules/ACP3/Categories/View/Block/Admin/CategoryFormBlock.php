<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\View\Block\Admin;

use ACP3\Core\Modules;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;

class CategoryFormBlock extends AbstractFormBlock
{
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var CategoriesRepository
     */
    private $categoriesRepository;

    /**
     * CategoryFormBlock constructor.
     * @param FormBlockContext $context
     * @param Modules $modules
     * @param CategoriesRepository $categoriesRepository
     */
    public function __construct(FormBlockContext $context, Modules $modules, CategoriesRepository $categoriesRepository)
    {
        parent::__construct($context);

        $this->modules = $modules;
        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->title->setPageTitlePrefix($data['title']);

        return [
            'form' => array_merge($data, $this->getRequestData()),
            'category_tree' => $this->fetchCategoryTree(
                $data['parent_id'],
                $data['left_id'],
                $data['right_id'],
                $data['module_id']
            ),
            'mod_list' => $this->fetchModules(),
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @return array
     */
    private function fetchModules()
    {
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $name => $info) {
            if ($info['active'] && in_array('categories', $info['dependencies']) === true) {
                $modules[$name]['selected'] = $this->forms->selectEntry('module_id', $info['id']);
            } else {
                unset($modules[$name]);
            }
        }
        return $modules;
    }

    /**
     * @param int $parentId
     * @param int $leftId
     * @param int $rightId
     * @param int $moduleId
     * @return array
     */
    private function fetchCategoryTree(int $parentId = 0, int $leftId = 0, int $rightId = 0, int $moduleId = 0): array
    {
        $categories = [];
        if ($moduleId !== 0) {
            $categories = $this->categoriesRepository->getAllByModuleId($moduleId);
            $cCategories = count($categories);
            for ($i = 0; $i < $cCategories; ++$i) {
                if ($categories[$i]['left_id'] >= $leftId && $categories[$i]['right_id'] <= $rightId) {
                    unset($categories[$i]);
                } else {
                    $categories[$i]['selected'] = $this->forms->selectEntry('parent_id', $categories[$i]['id'], $parentId);
                    $categories[$i]['title'] = str_repeat('&nbsp;&nbsp;', $categories[$i]['level']) . $categories[$i]['title'];
                }
            }
        }

        return $categories;
    }


    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return ['title' => '', 'description' => '', 'parent_id' => 0, 'left_id' => 0, 'right_id' => 0, 'module_id' => 0];
    }
}
