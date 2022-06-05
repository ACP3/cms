<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\Context;
use ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter;

class AbstractFormAction extends AbstractWidgetAction
{
    public function __construct(Context $context, private readonly SendNewsletter $newsletterHelpers)
    {
        parent::__construct($context);
    }

    /**
     * @return array{string, bool}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function sendTestNewsletter(bool $isTest, int $id, bool $dbResult, string $testEmailAddress): array
    {
        if ($isTest === true) {
            $result = $this->newsletterHelpers->sendNewsletter($id, $testEmailAddress);

            $text = $this->translator->t('newsletter', 'create_success');
            $result = $dbResult !== false && $result !== false;
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
