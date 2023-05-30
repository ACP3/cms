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

class MaintenanceModeDisableCommand extends Command
{
    public function __construct(private readonly SettingsInterface $settings)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('acp3:maintenance:disable')
            ->setDescription('Disables the maintenance mode of the ACP3.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Disabling maintenance mode...');

        $result = $this->settings->saveSettings(
            ['maintenance_mode' => 0],
            Schema::MODULE_NAME
        );

        $output->writeln($result ? 'Disabled the maintenance mode!' : 'Error while disabling the maintenance mode!');
        $output->writeln('');

        $this->clearCaches($output);

        return 0;
    }

    private function clearCaches(OutputInterface $output): void
    {
        $command = $this->getApplication()->find('acp3:cache:clear');
        $command->run(new ArrayInput([]), $output);
    }
}
