<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Console\Command;

use ACP3\Core\Application\Bootstrap;
use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class DumpContainerCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('acp3:cache:warmup')
            ->setDescription('This CLI-command internally boots the application, dumps the DI container and warms all caches for the homepage, to improve the first time load performance after a new deployment.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $kernel = new Bootstrap(ApplicationMode::PRODUCTION);
            $kernel->handle(Request::createFromGlobals());

            return 0;
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());

            return 1;
        }
    }
}
