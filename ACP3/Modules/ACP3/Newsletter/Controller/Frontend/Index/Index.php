<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe
     */
    protected $subscribeHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\SubscribeFormValidation
     */
    protected $subscribeFormValidation;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;
    /**
     * @var Core\Helpers\Alerts
     */
    private $alerts;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param Core\Helpers\Alerts $alerts
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe $subscribeHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\SubscribeFormValidation $subscribeFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Core\Helpers\Alerts $alerts,
        Newsletter\Helper\Subscribe $subscribeHelper,
        Newsletter\Validation\SubscribeFormValidation $subscribeFormValidation
    ) {
        parent::__construct($context);

        $this->subscribeHelper = $subscribeHelper;
        $this->subscribeFormValidation = $subscribeFormValidation;
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

                $this->subscribeFormValidation->validate($formData);

                $bool = $this->subscribeHelper->subscribeToNewsletter(
                    $formData['mail'],
                    $formData['salutation'],
                    $formData['first_name'],
                    $formData['last_name']
                );

                $this->view->setTemplate($this->alerts->confirmBox(
                    $this->translator->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'),
                    $this->appPath->getWebRoot())
                );
            }
        );
    }
}
