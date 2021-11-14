<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Console;

use ACP3\Modules\ACP3\System\Services\CacheClearService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AssetsClearCommand extends Command
{
    public function __construct(private CacheClearService $cacheClearService)
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('acp3:assets:clear')
            ->setDescription('Clears all locally stored assets.')
            ->setHelp('This command allows you to clear all the locally stored assets.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Clearing generated assets...');

        $this->cacheClearService->clearCacheByType('minify');

        $output->writeln('Done!');

        return 0;
    }
}
