<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\Context;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Contact;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class IndexPost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Context $context,
        private FormAction $actionHelper,
        private Core\Router\RouterInterface $router,
        private Core\Helpers\Alerts $alertsHelper,
        private Contact\Validation\FormValidation $formValidation,
        private Contact\Model\ContactsModel $contactsModel,
        private Contact\Model\ContactFormModel $contactFormModel
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(): array|string|Response
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
