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

class MergePackageJsonCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('acp3:components:merge-package-json')
            ->setDescription('This CLI command merges the package.json files of the various ACP3 components into a single one.');
    }

    /**
     * @throws \JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $basePackageJsonFile = ACP3_ROOT_DIR . '/package-base.json';

        if (!is_file($basePackageJsonFile)) {
            $output->writeln("<error>Could not find package-base.json in the root ACP3 folder.\nPlease create one at first!</error>");

            return 1;
        }

        $basePackageJson = json_decode(
            file_get_contents($basePackageJsonFile),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $result = $basePackageJson;
        foreach (ComponentRegistry::allTopSorted() as $component) {
            $path = $component->getPath() . '/package.json';
            if (!is_file($path)) {
                continue;
            }

            $json = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

            if (isset($json['dependencies'])) {
                $result['dependencies'] = array_merge($result['dependencies'] ?? [], $json['dependencies']);
                ksort($result['dependencies']);
            }
            if (isset($json['devDependencies'])) {
                $result['devDependencies'] = array_merge($result['devDependencies'] ?? [], $json['devDependencies']);
                ksort($result['devDependencies']);
            }
            if (isset($json['scripts'])) {
                $result['scripts'] = array_merge($result['scripts'] ?? [], $json['scripts']);
                ksort($result['scripts']);
            }
        }

        file_put_contents(
            ACP3_ROOT_DIR . '/package.json',
            json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES ^ JSON_PRETTY_PRINT) . "\n",
        );

        return 0;
    }
}
