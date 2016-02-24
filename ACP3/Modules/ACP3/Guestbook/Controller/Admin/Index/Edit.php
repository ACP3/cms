<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\Guestbook;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index
 */
class Edit extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository
     */
    protected $guestbookRepository;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                  $context
     * @param \ACP3\Core\Helpers\FormToken                                $formTokenHelper
     * @param \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository      $guestbookRepository
     * @param \ACP3\Modules\ACP3\Guestbook\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Guestbook\Model\GuestbookRepository $guestbookRepository,
        Guestbook\Validation\AdminFormValidation $adminFormValidation
    )
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->guestbookRepository = $guestbookRepository;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @param \ACP3\Modules\ACP3\Emoticons\Helpers $emoticonsHelpers
     *
     * @return $this
     */
    public function setEmoticonsHelpers(Emoticons\Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;

        return $this;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $guestbook = $this->guestbookRepository->getOneById($id);
        if (empty($guestbook) === false) {
            $settings = $this->config->getSettings('guestbook');

            $this->breadcrumb->setTitlePostfix($guestbook['name']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $settings, $id);
            }

            if ($settings['emoticons'] == 1 && $this->emoticonsHelpers) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
            }

            if ($settings['notify'] == 2) {
                $this->view->assign('activate', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('active', $guestbook['active']));
            }

            return [
                'form' => array_merge($guestbook, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $settings, $id) {
            $this->adminFormValidation
                ->setSettings($settings)
                ->validate($formData);

            $updateValues = [
                'message' => Core\Functions::strEncode($formData['message']),
                'active' => $settings['notify'] == 2 ? $formData['active'] : 1,
            ];

            $bool = $this->guestbookRepository->update($updateValues, $id);

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
