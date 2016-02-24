<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core\Controller\AdminAction;
use ACP3\Core\Controller\Context\AdminContext;
use ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index
 */
class AbstractFormAction extends AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter
     */
    protected $newsletterHelpers;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext          $context
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter $newsletterHelpers
     */
    public function __construct(AdminContext $context, SendNewsletter $newsletterHelpers)
    {
        parent::__construct($context);

        $this->newsletterHelpers = $newsletterHelpers;
    }

    /**
     * @param bool   $isTest
     * @param int    $id
     * @param bool   $dbResult
     * @param string $testEmailAddress
     *
     * @return array
     */
    protected function sendTestNewsletter($isTest, $id, $dbResult, $testEmailAddress)
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
            $result
        ];
    }
}