<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Edit extends AbstractFormAction
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
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param Newsletter\Model\NewslettersModel $newsletterModel
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter $newsletterHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
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
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $newsletter = $this->newsletterModel->getOneById($id);

        if (empty($newsletter) === false) {
            return $this->block
                ->setRequestData($this->request->getPost()->all())
                ->setData($newsletter)
                ->render();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost($id)
    {
        return $this->actionHelper->handlePostAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $settings = $this->config->getSettings(Newsletter\Installer\Schema::MODULE_NAME);

            $this->adminFormValidation->validate($formData);

            $formData['user_id'] = $this->user->getUserId();
            $bool = $this->newsletterModel->save($formData, $id);

            list($text, $result) = $this->sendTestNewsletter(
                $formData['test'] == 1,
                $id,
                $bool,
                $settings['mail']
            );

            return $this->redirectMessages()->setMessage($result, $text);
        });
    }
}
