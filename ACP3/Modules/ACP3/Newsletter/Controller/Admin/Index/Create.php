<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var Newsletter\Model\NewslettersModel
     */
    protected $newsletterModel;
    /**
     * @var Core\View\Block\AdminFormBlockInterface
     */
    private $block;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\AdminFormBlockInterface $block
     * @param Newsletter\Model\NewslettersModel $newsletterModel
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter $newsletterHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\AdminFormBlockInterface $block,
        Newsletter\Model\NewslettersModel $newsletterModel,
        Newsletter\Validation\AdminFormValidation $adminFormValidation,
        Newsletter\Helper\SendNewsletter $newsletterHelpers
    ) {
        parent::__construct($context, $newsletterHelpers);

        $this->newsletterModel = $newsletterModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->block = $block;
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
        return $this->actionHelper->handlePostAction(function () {
            $formData = $this->request->getPost()->all();

            $settings = $this->config->getSettings(Newsletter\Installer\Schema::MODULE_NAME);

            $this->adminFormValidation->validate($formData);

            $formData['user_id'] = $this->user->getUserId();
            $newsletterId = $this->newsletterModel->save($formData);

            list($text, $result) = $this->sendTestNewsletter(
                $formData['test'] == 1,
                $newsletterId,
                $newsletterId,
                $settings['mail']
            );

            return $this->redirectMessages()->setMessage($result, $text);
        });
    }
}
