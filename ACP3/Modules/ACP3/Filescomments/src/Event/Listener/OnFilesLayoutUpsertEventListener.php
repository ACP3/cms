<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filescomments\Event\Listener;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Filescomments\Installer\Schema;

class OnFilesLayoutUpsertEventListener
{
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Modules $modules, View $view, SettingsInterface $settings, Forms $formsHelper)
    {
        $this->view = $view;
        $this->formsHelper = $formsHelper;
        $this->settings = $settings;
        $this->modules = $modules;
    }

    public function __invoke(TemplateEvent $event): void
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
            $formData = $event->getParameters()['form_data'];

            $this->view->assign(
                'comments',
                $this->formsHelper->yesNoCheckboxGenerator(
                    'comments',
                    $formData['comments'] ?? (int) $settings['comments']
                )
            );

            $this->view->displayTemplate('Filescomments/Partials/files_layout_upsert.tpl');
        }
    }
}
