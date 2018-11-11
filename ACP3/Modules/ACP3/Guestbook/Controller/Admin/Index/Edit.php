<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Guestbook;

class Edit extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Guestbook\Model\GuestbookModel
     */
    private $guestbookModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Guestbook\Model\GuestbookModel $guestbookModel,
        Guestbook\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->guestbookModel = $guestbookModel;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        $guestbook = $this->guestbookModel->getOneById($id);
        if (empty($guestbook) === false) {
            $settings = $this->config->getSettings(Guestbook\Installer\Schema::MODULE_NAME);

            $this->title->setPageTitlePrefix($guestbook['name']);

            return [
                'form' => \array_merge($guestbook, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
                'can_use_emoticons' => $settings['emoticons'] == 1,
                'activate' => $settings['notify'] == 2
                    ? $this->formsHelper->yesNoCheckboxGenerator('active', $guestbook['active'])
                    : [],
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $settings = $this->config->getSettings(Guestbook\Installer\Schema::MODULE_NAME);

            $this->adminFormValidation
                ->setSettings($settings)
                ->validate($formData);

            $formData['active'] = $settings['notify'] == 2 ? $formData['active'] : 1;

            $bool = $this->guestbookModel->save($formData, $id);

            return $bool;
        });
    }
}
