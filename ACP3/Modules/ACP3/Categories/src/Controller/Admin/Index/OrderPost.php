<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Http\RedirectResponse;
use ACP3\Modules\ACP3\Categories\Model\CategoriesModel;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;

class OrderPost extends AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Repository\CategoryRepository
     */
    private $categoriesRepository;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoriesModel
     */
    private $categoriesModel;

    public function __construct(
        WidgetContext $context,
        RedirectResponse $redirectResponse,
        CategoryRepository $categoriesRepository,
        CategoriesModel $categoriesModel
    ) {
        parent::__construct($context);

        $this->categoriesRepository = $categoriesRepository;
        $this->redirectResponse = $redirectResponse;
        $this->categoriesModel = $categoriesModel;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id, string $action)
    {
        if (($action === 'up' || $action === 'down') && $this->categoriesRepository->resultExists($id) === true) {
            if ($action === 'up') {
                $this->categoriesModel->moveUp($id);
            } else {
                $this->categoriesModel->moveDown($id);
            }

            return $this->redirectResponse->temporary('acp/categories');
        }

        throw new ResultNotExistsException();
    }
}
