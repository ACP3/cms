<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Console\Command;

use ACP3\Core\Migration\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ModulesUpdateCommand extends Command
{
    /**
     * @var Migrator
     */
    private $migrator;

    public function __construct(Migrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('acp3:modules:update')
            ->setDescription('Updates the database schema of all currently installed modules.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Updating installed modules...');

        $executedMigrations = $this->migrator->updateModules();

        if (\count($executedMigrations) === 0) {
            $output->writeln('Everything is already up to date!');

            return 0;
        }

        $hasErrors = $this->displayExecutedMigrations($executedMigrations, $output);

        return $hasErrors ? 1 : 0;
    }

    /**
     * @param array<string, \Throwable[]|null> $executedMigrations
     */
    private function displayExecutedMigrations(array $executedMigrations, OutputInterface $output): bool
    {
        $hasErrors = false;
        foreach ($executedMigrations as $migrationFqcn => $result) {
            $output->writeln($result === null ? "<info>{$migrationFqcn}</info>" : "<error>{$migrationFqcn}</error>");

            if ($result !== null) {
                $hasErrors = true;

                foreach ($result as $error) {
                    $output->writeln("<error>{$error->getMessage()}</error>");
                }
            }
        }

        return $hasErrors;
    }
}
