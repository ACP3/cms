<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Helpers\Forms;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;

class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    private $categoryRepository;

    public function __construct(FrontendContext $context, Forms $formsHelper, CategoryRepository $categoryRepository)
    {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function fetchCategoryTree(
        ?int $moduleId = null,
        ?int $parentId = null,
        ?int $leftId = null,
        ?int $rightId = null
    ): array {
        $categories = [];
        if ($moduleId !== null) {
            $categories = $this->categoryRepository->getAllByModuleId($moduleId);
            $cCategories = \count($categories);
            for ($i = 0; $i < $cCategories; ++$i) {
                if ($categories[$i]['left_id'] >= $leftId && $categories[$i]['right_id'] <= $rightId) {
                    unset($categories[$i]);
                } else {
                    $categories[$i]['selected'] = $this->formsHelper->selectEntry('parent_id', $categories[$i]['id'], $parentId);
                    $categories[$i]['title'] = \str_repeat('&nbsp;&nbsp;', $categories[$i]['level']) . $categories[$i]['title'];
                }
            }
        }

        return $categories;
    }
}
