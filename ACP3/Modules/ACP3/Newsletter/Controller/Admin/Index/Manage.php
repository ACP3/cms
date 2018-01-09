<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;
use Doctrine\DBAL\DBALException;

class Manage extends Core\Controller\AbstractFrontendAction
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
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;
    /**
     * @var Newsletter\Helper\SendNewsletter
     */
    private $newsletterHelpers;

    /**
     * Manage constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface            $block
     * @param Newsletter\Model\NewslettersModel                            $newsletterModel
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter          $newsletterHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Newsletter\Model\NewslettersModel $newsletterModel,
        Newsletter\Validation\AdminFormValidation $adminFormValidation,
        Newsletter\Helper\SendNewsletter $newsletterHelpers
    ) {
        parent::__construct($context);

        $this->newsletterModel = $newsletterModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->block = $block;
        $this->newsletterHelpers = $newsletterHelpers;
    }

    /**
     * @param int|null $id
     *
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function execute(?int $id)
    {
        return $this->block
            ->setDataById($id)
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param int|null $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(?int $id)
    {
        return $this->actionHelper->handlePostAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $settings = $this->config->getSettings(Newsletter\Installer\Schema::MODULE_NAME);

            $this->adminFormValidation->validate($formData);

            $formData['user_id'] = $this->user->getUserId();
            try {
                $result = $this->newsletterModel->save($formData, $id);

                list($text, $result) = $this->sendTestNewsletter(
                    $formData['test'] == 1, $result, $settings['mail']
                );
            } catch (DBALException $e) {
                $result = false;
                $text = $this->translator->t('newsletter', 'create_save_error');
            }

            return $this->redirectMessages()->setMessage($result, $text);
        });
    }

    /**
     * @param bool   $isTest
     * @param int    $id
     * @param string $testEmailAddress
     *
     * @return array
     */
    private function sendTestNewsletter(bool $isTest, int $id, string $testEmailAddress)
    {
        if ($isTest === true) {
            $result = $this->newsletterHelpers->sendNewsletter($id, $testEmailAddress);
            $text = $this->translator->t('newsletter', 'create_success');
        } else {
            $result = true;
            $text = $this->translator->t('newsletter', 'save_success');
        }

        if ($result === false) {
            $text = $this->translator->t('newsletter', 'create_save_error');
        }

        return [
            $text,
            $result,
        ];
    }
}
