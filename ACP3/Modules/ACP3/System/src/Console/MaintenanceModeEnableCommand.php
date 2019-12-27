<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Console;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MaintenanceModeEnableCommand extends Command
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * MaintenanceModeEnableCommand constructor.
     */
    public function __construct(SettingsInterface $settings)
    {
        parent::__construct();

        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('acp3:maintenance:enable')
            ->setDescription('Enables the maintenance mode of the ACP3.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Enabling maintenance mode...');

        $result = $this->settings->saveSettings(
            ['maintenance_mode' => 1],
            Schema::MODULE_NAME
        );

        $output->writeln($result ? 'Enabled the maintenance mode!' : 'Error while enabling the maintenance mode!');
        $output->writeln('');

        $this->clearCaches($output);

        return 0;
    }

    private function clearCaches(OutputInterface $output)
    {
        $command = $this->getApplication()->find('acp3:cache:clear');
        $command->run(new ArrayInput([]), $output);
    }
}
