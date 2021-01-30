<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Contact;

class IndexPost extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
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
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        WidgetContext $context,
        Action $actionHelper,
        Core\Router\RouterInterface $router,
        Core\Helpers\Alerts $alertsHelper,
        Contact\Validation\FormValidation $formValidation,
        Contact\Model\ContactsModel $contactsModel,
        Contact\Model\ContactFormModel $contactFormModel
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->contactFormModel = $contactFormModel;
        $this->contactsModel = $contactsModel;
        $this->alertsHelper = $alertsHelper;
        $this->router = $router;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();
                $this->formValidation->validate($formData);

                $this->contactsModel->save($formData);

                $result = $this->contactFormModel->sendContactFormEmail($formData);
                $this->contactFormModel->sendContactFormEmailCopy($formData);

                return $this->alertsHelper->confirmBox(
                    $this->translator->t('contact', $result === true ? 'send_mail_success' : 'send_mail_error'),
                    $this->router->route('contact')
                );
            }
        );
    }
}
