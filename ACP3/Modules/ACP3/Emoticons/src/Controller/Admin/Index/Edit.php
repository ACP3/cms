<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Emoticons;

class Edit extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Emoticons\Model\EmoticonsModel
     */
    private $emoticonsModel;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\ViewProviders\AdminEmoticonEditViewProvider
     */
    private $adminEmoticonEditViewProvider;

    public function __construct(
        WidgetContext $context,
        Emoticons\Model\EmoticonsModel $emoticonsModel,
        Emoticons\ViewProviders\AdminEmoticonEditViewProvider $adminEmoticonEditViewProvider
    ) {
        parent::__construct($context);

        $this->emoticonsModel = $emoticonsModel;
        $this->adminEmoticonEditViewProvider = $adminEmoticonEditViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        $emoticon = $this->emoticonsModel->getOneById($id);

        if (empty($emoticon) === false) {
            return ($this->adminEmoticonEditViewProvider)($emoticon);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
