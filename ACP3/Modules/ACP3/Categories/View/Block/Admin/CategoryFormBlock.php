<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\View\Block\Admin;

use ACP3\Core\Modules;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;

class CategoryFormBlock extends AbstractFormBlock
{
    /**
     * @var Modules
     */
    private $modules;

    /**
     * CategoryFormBlock constructor.
     * @param FormBlockContext $context
     * @param Modules $modules
     */
    public function __construct(FormBlockContext $context, Modules $modules)
    {
        parent::__construct($context);

        $this->modules = $modules;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->title->setPageTitlePrefix($data['title']);

        return [
            'form' => array_merge($data, $this->getRequestData()),
            'mod_list' => $this->fetchModules(),
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @return array
     */
    private function fetchModules()
    {
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $name => $info) {
            if ($info['active'] && in_array('categories', $info['dependencies']) === true) {
                $modules[$name]['selected'] = $this->forms->selectEntry('module', $info['id']);
            } else {
                unset($modules[$name]);
            }
        }
        return $modules;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return ['title' => '', 'description' => ''];
    }
}
