<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Console\Command;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\Dto\ComponentDataDto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComponentPathsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('acp3:components:paths')
            ->setDescription('Save a list with the filesystem paths of all registered components as a JSON file.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paths = \array_map(static function (ComponentDataDto $component) {
            return \str_replace([ACP3_ROOT_DIR, '\\'], ['.', '/'], $component->getPath());
        }, ComponentRegistry::all());

        \file_put_contents(
            ACP3_ROOT_DIR . '/.component-paths.json',
            \json_encode(\array_values($paths), JSON_PRETTY_PRINT)
        );

        return 0;
    }
}
