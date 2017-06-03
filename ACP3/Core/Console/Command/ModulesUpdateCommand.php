<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
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
     * ClearCacheCommand constructor.
     * @param SchemaUpdateModel $schemaUpdateModel
     * @internal param ApplicationPath $applicationPath
     */
    public function __construct(SchemaUpdateModel $schemaUpdateModel)
    {
        parent::__construct();

        $this->schemaUpdateModel = $schemaUpdateModel;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('acp3:modules:update')
            ->setDescription('Updates the database schema of all currently installed modules.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Updating installed modules...');

        foreach ($this->schemaUpdateModel->updateModules() as $module => $result) {
            $output->writeln($result === 1 ? "<info>{$module}</info>" : "<error>{$module}</error>");
        }
    }
}
