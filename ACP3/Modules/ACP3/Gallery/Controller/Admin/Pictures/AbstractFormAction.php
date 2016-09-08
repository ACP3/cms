<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;


use ACP3\Core\Controller\AbstractAdminAction;
use ACP3\Core\Controller\Context\AdminContext;
use ACP3\Core\Helpers\Forms;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures
 */
class AbstractFormAction extends AbstractAdminAction
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\Forms                   $formsHelper
     */
    public function __construct(AdminContext $context, Forms $formsHelper)
    {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
    }

    /**
     * @param string $currentValue
     *
     * @return array
     */
    protected function getOptions($currentValue = '0')
    {
        $comments = [
            '1' => $this->translator->t('system', 'allow_comments')
        ];

        return $this->formsHelper->checkboxGenerator('comments', $comments, $currentValue);
    }
}
