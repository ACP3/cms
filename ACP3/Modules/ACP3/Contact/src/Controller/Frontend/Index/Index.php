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
    private $formValidation;
    /**
     * @var Contact\Model\ContactFormModel
     */
    private $contactFormModel;
    /**
     * @var Contact\Model\ContactsModel
     */
    private $contactsModel;
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alertsHelper;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Modules\ACP3\Contact\ViewProviders\ContactFormViewProvider
     */
    private $contactFormViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Router\RouterInterface $router,
        Core\Helpers\Alerts $alertsHelper,
        Contact\Validation\FormValidation $formValidation,
        Contact\Model\ContactsModel $contactsModel,
        Contact\Model\ContactFormModel $contactFormModel,
        Contact\ViewProviders\ContactFormViewProvider $contactFormViewProvider
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->contactFormModel = $contactFormModel;
        $this->contactsModel = $contactsModel;
        $this->alertsHelper = $alertsHelper;
        $this->router = $router;
        $this->contactFormViewProvider = $contactFormViewProvider;
    }

    public function execute(): array
    {
        return ($this->contactFormViewProvider)();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();
                $this->formValidation->validate($formData);

                $this->contactsModel->save($formData);

                $bool = $this->contactFormModel->sendContactFormEmail($formData);
                $this->contactFormModel->sendContactFormEmailCopy($formData);

                $this->setTemplate($this->alertsHelper->confirmBox(
                    $this->translator->t('contact', $bool === true ? 'send_mail_success' : 'send_mail_error'),
                    $this->router->route('contact')
                ));
            }
        );
    }
}
