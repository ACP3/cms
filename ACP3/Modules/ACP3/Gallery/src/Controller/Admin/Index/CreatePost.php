<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Gallery;

class CreatePost extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\GalleryFormValidation
     */
    private $galleryFormValidation;
    /**
     * @var Gallery\Model\GalleryModel
     */
    private $galleryModel;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;
    /**
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        FormAction $actionHelper,
        UserModelInterface $user,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\Validation\GalleryFormValidation $galleryFormValidation
    ) {
        parent::__construct($context);

        $this->galleryModel = $galleryModel;
        $this->galleryFormValidation = $galleryFormValidation;
        $this->user = $user;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->galleryFormValidation->validate($formData);

            $formData['user_id'] = $this->user->getUserId();

            return $this->galleryModel->save($formData);
        });
    }
}
