<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Console\Command;

use ACP3\Core\Cache\Purge;
use ACP3\Core\Environment\ApplicationPath;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheClearCommand extends Command
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
            ->setName('acp3:cache:clear')
            ->setDescription('Clears all locally stored caches.')
            ->setHelp('This command allows you to clear all the locally stored caches. This applies for the PhpFileCache only');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Clearing paths...');

        $paths = glob(ACP3_ROOT_DIR . 'cache/*/*');
        $length = array_push($paths, $this->applicationPath->getUploadsDir() . 'assets');

        $progress = new ProgressBar($output, $length);
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% -- %message%');
        $progress->setFormat('custom');

        foreach ($paths as $path) {
            $progress->setMessage($path);

            Purge::doPurge($path);

            $progress->advance();
        }

        $progress->finish();
    }
}
