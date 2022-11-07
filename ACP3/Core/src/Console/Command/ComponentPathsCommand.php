<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Console\Command;

use ACP3\Core\Component\ComponentRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComponentPathsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('acp3:components:paths')
            ->setDescription('Save a list with the filesystem paths of all registered components as a JSON file.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = [];
        foreach (ComponentRegistry::all() as $component) {
            if (\array_key_exists($component->getComponentType()->value, $paths) === false) {
                $paths[$component->getComponentType()->value] = [];
            }

            $path = $component->getPath() . '/composer.json';
            $content = file_get_contents($path);

            if ($content === false) {
                throw new \RuntimeException(sprintf('Could not read file "%s"!', $path));
            }

            $composer = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            $paths[$component->getComponentType()->value][$composer['name']] = str_replace([ACP3_ROOT_DIR, '\\'], ['.', '/'], $component->getPath());
        }

        $result = file_put_contents(
            ACP3_ROOT_DIR . '/.component-paths.json',
            json_encode($paths, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES ^ JSON_PRETTY_PRINT)
        );

        return $result !== false ? 0 : 1;
    }
}
