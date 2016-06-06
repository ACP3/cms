<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Widget\Index
 */
class Index extends Core\Controller\WidgetAction
{
    /**
     * @var Core\Helpers\FormToken
     */
    protected $formTokenHelper;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param \ACP3\Core\Helpers\FormToken                $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @param string $template
     *
     * @return array
     */
    public function execute($template = '')
    {
        $this->setTemplate($template !== '' ? $template : 'Newsletter/Widget/index.index.tpl');

        return [
            'form_token' => $this->formTokenHelper->renderFormToken('newsletter/index/index')
        ];
    }
}
