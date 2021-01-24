<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Controller\Installer\Index;

use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Core\Date;
use ACP3\Core\Helpers\Date as DateHelper;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\XML;
use ACP3\Modules\ACP3\Installer\Core\Controller\Context\InstallerContext;
use ACP3\Modules\ACP3\Installer\Helpers\Navigation;
use ACP3\Modules\ACP3\System\Helper\AvailableDesignsTrait;

class Install extends AbstractAction implements InvokableActionInterface
{
    use AvailableDesignsTrait;

    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var XML
     */
    private $xml;
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    private $dateHelper;
    /**
     * @var Forms
     */
    private $forms;

    public function __construct(
        InstallerContext $context,
        Navigation $navigation,
        Date $date,
        XML $xml,
        DateHelper $dateHelper,
        Forms $forms
    ) {
        parent::__construct($context, $navigation);

        $this->date = $date;
        $this->xml = $xml;
        $this->dateHelper = $dateHelper;
        $this->forms = $forms;
    }

    public function __invoke(): array
    {
        return [
            'time_zones' => $this->dateHelper->getTimeZones(\date_default_timezone_get()),
            'form' => \array_merge($this->getFormDefaults(), $this->request->getPost()->all()),
            'designs' => $this->getThemeFormOptions(),
        ];
    }

    private function getFormDefaults(): array
    {
        return [
            'db_host' => 'localhost',
            'db_pre' => '',
            'db_user' => '',
            'db_name' => '',
            'user_name' => '',
            'mail' => '',
            'date_format_long' => $this->date->getDateFormatLong(),
            'date_format_short' => $this->date->getDateFormatShort(),
            'title' => 'ACP3',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getXml()
    {
        return $this->xml;
    }

    /**
     * {@inheritdoc}
     */
    protected function selectEntry($directory)
    {
        return $this->forms->selectEntry('design', $directory);
    }

    protected function getThemeFormOptions(): array
    {
        $themes = $this->getAvailableDesigns();

        $options = [];
        foreach ($themes as $theme) {
            $options[$theme['dir']] = $theme['name'];
        }

        return $this->forms->choicesGenerator('design', $options);
    }
}
