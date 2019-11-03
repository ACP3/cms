<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Console\Command;

use ACP3\Core\Installer\Model\SchemaUpdateModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ModulesUpdateCommand extends Command
{
    /**
     * @var SchemaUpdateModel
     */
    private $schemaUpdateModel;

    /**
     * ModulesUpdateCommand constructor.
     */
    public function __construct(SchemaUpdateModel $schemaUpdateModel)
    {
        parent::__construct();

        $this->schemaUpdateModel = $schemaUpdateModel;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('acp3:modules:update')
            ->setDescription('Updates the database schema of all currently installed modules.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Updating installed modules...');

        $hasError = false;

        foreach ($this->schemaUpdateModel->updateModules() as $module => $result) {
            $output->writeln($result ? "<info>{$module}</info>" : "<error>{$module}</error>");

            if (!$result) {
                $hasError = true;
            }
        }

        return $hasError ? 1 : 0;
    }
}
