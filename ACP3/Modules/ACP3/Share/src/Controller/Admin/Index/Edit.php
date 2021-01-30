<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Share\Model\ShareModel;
use ACP3\Modules\ACP3\Share\ViewProviders\AdminShareEditViewProvider;

class Edit extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\ShareModel
     */
    private $shareModel;
    /**
     * @var \ACP3\Modules\ACP3\Share\ViewProviders\AdminShareEditViewProvider
     */
    private $adminShareEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        ShareModel $shareModel,
        AdminShareEditViewProvider $adminShareEditViewProvider
    ) {
        parent::__construct($context);

        $this->shareModel = $shareModel;
        $this->adminShareEditViewProvider = $adminShareEditViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        $sharingInfo = $this->shareModel->getOneById($id);

        if (empty($sharingInfo) === false) {
            return ($this->adminShareEditViewProvider)($sharingInfo);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
