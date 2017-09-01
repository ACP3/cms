<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Console\Command;

use ACP3\Core\Cache\Purge;
use ACP3\Core\Environment\ApplicationPath;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AssetsClearCommand extends Command
{
    /**
     * @var ApplicationPath
     */
    private $applicationPath;

    /**
     * ClearCacheCommand constructor.
     * @param ApplicationPath $applicationPath
     */
    public function __construct(ApplicationPath $applicationPath)
    {
        parent::__construct();

        $this->applicationPath = $applicationPath;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('acp3:assets:clear')
            ->setDescription('Clears all locally stored assets.')
            ->setHelp('This command allows you to clear all the locally stored assets.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Clearing assets...');

        Purge::doPurge($this->applicationPath->getUploadsDir() . 'assets');

        $output->writeln('Done!');
    }
}
