<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index
 */
class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe
     */
    protected $subscribeHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\SubscribeFormValidation
     */
    protected $subscribeFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                    $context
     * @param \ACP3\Core\Helpers\Forms                                         $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                     $formTokenHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe                   $subscribeHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\SubscribeFormValidation $subscribeFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Helper\Subscribe $subscribeHelper,
        Newsletter\Validation\SubscribeFormValidation $subscribeFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->subscribeHelper = $subscribeHelper;
        $this->subscribeFormValidation = $subscribeFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $defaults = [
            'first_name' => '',
            'last_name' => '',
            'mail' => ''
        ];

        $salutations = [
            0 => $this->translator->t('newsletter', 'salutation_unspecified'),
            1 => $this->translator->t('newsletter', 'salutation_female'),
            2 => $this->translator->t('newsletter', 'salutation_male')
        ];

        return [
            'salutation' => $this->formsHelper->choicesGenerator('salutation', $salutations),
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->subscribeFormValidation->validate($formData);

                $bool = $this->subscribeHelper->subscribeToNewsletter(
                    $formData['mail'],
                    $formData['salutation'],
                    $formData['first_name'],
                    $formData['last_name']
                );

                $this->setTemplate($this->get('core.helpers.alerts')->confirmBox(
                    $this->translator->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'),
                    $this->appPath->getWebRoot())
                );
            }
        );
    }
}
