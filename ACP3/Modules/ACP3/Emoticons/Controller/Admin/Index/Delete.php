<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Emoticons\Model\EmoticonsModel
     */
    protected $emoticonsModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Emoticons\Model\EmoticonsModel $emoticonsModel
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Emoticons\Model\EmoticonsModel $emoticonsModel
    ) {
        parent::__construct($context);

        $this->emoticonsModel = $emoticonsModel;
    }

    /**
     * @param string $action
     *
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|void
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->emoticonsModel->delete($items);
            }
        );
    }
}
