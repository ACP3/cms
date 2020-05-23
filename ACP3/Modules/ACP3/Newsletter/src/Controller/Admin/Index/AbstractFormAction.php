<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter;

class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter
     */
    private $newsletterHelpers;

    public function __construct(FrontendContext $context, SendNewsletter $newsletterHelpers)
    {
        parent::__construct($context);

        $this->newsletterHelpers = $newsletterHelpers;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function sendTestNewsletter(bool $isTest, int $id, bool $dbResult, string $testEmailAddress): array
    {
        if ($isTest === true) {
            $bool2 = $this->newsletterHelpers->sendNewsletter($id, $testEmailAddress);

            $text = $this->translator->t('newsletter', 'create_success');
            $result = $dbResult !== false && $bool2 !== false;
        } else {
            $text = $this->translator->t('newsletter', 'save_success');
            $result = $dbResult !== false;
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
