<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Contact\Validation\FormValidation
     */
    protected $formValidation;
    /**
     * @var Contact\Model\ContactFormModel
     */
    protected $contactFormModel;
    /**
     * @var Contact\Model\ContactsModel
     */
    protected $contactsModel;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;
    /**
     * @var Core\Helpers\Alerts
     */
    private $alerts;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext        $context
     * @param Core\View\Block\FormBlockInterface                   $block
     * @param Core\Helpers\Alerts                                  $alerts
     * @param \ACP3\Modules\ACP3\Contact\Validation\FormValidation $formValidation
     * @param Contact\Model\ContactsModel                          $contactsModel
     * @param Contact\Model\ContactFormModel                       $contactFormModel
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Core\Helpers\Alerts $alerts,
        Contact\Validation\FormValidation $formValidation,
        Contact\Model\ContactsModel $contactsModel,
        Contact\Model\ContactFormModel $contactFormModel
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->contactFormModel = $contactFormModel;
        $this->contactsModel = $contactsModel;
        $this->block = $block;
        $this->alerts = $alerts;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();
                $this->formValidation->validate($formData);

                $this->contactsModel->save($formData);

                $bool = $this->contactFormModel->sendContactFormEmail($formData);

                if (isset($formData['copy'])) {
                    $this->contactFormModel->sendContactFormEmailCopy($formData);
                }

                return $this->alerts->confirmBox(
                    $this->translator->t('contact', $bool === true ? 'send_mail_success' : 'send_mail_error'),
                    $this->router->route('contact')
                );
            }
        );
    }
}
