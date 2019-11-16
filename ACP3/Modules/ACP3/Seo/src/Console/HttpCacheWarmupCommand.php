<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class HttpCacheWarmupCommand extends Command
{
    private $defaultSitemapName = ACP3_ROOT_DIR . '/sitemap.xml';

    private $splitSitemapNames = [
        ACP3_ROOT_DIR . '/sitemap_http.xml',
        ACP3_ROOT_DIR . '/sitemap_https.xml',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('acp3:http-cache:warmup')
            ->setDescription('Warms up the HTTP cache.')
            ->setHelp('This command reads the sitemap.xml and crawls the URLs in it, so that the HTTP cache gets a warming after a deployment.');

        $this
            ->addOption(
                'sleep',
                null,
                InputOption::VALUE_REQUIRED,
                'How many seconds should the crawler pause after crawling an URL?',
                0
            )
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'How many URLs per sitemap should be crawled?',
                0
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Warming up the HTTP cache...');

        if (\is_file($this->defaultSitemapName)) {
            $this->processSitemap($this->defaultSitemapName, $input, $output);
        } else {
            foreach ($this->splitSitemapNames as $sitemap) {
                if (\is_file($sitemap)) {
                    $this->processSitemap($sitemap, $input, $output);
                }
            }
        }
    }

    /**
     * Parses the XML sitemap and crawls the URLs of it.
     */
    private function processSitemap(string $sitemap, InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Crawling URLs of file {$sitemap}...");

        $xml = \simplexml_load_string(\file_get_contents($sitemap));

        $progress = new ProgressBar($output, \count($xml->url));
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% -- %message%: %result%');
        $progress->setFormat('custom');

        $sleep = $this->getSleepTime($input);
        $limit = (int) $input->getOption('limit');

        $i = 1;
        foreach ($xml->url as $url) {
            $progress->setMessage($url->loc);
            $progress->setMessage($this->crawlUrl($url->loc) ? 'Done!' : 'Error!', 'result');

            $progress->advance();

            if ($limit > 0 && $i === $limit) {
                break;
            }

            ++$i;

            if ($sleep > 0) {
                \usleep($sleep);
            }
        }

        $progress->finish();

        $output->writeln('');
    }

    /**
     * Crawls the given URL.
     *
     * @return bool Whether the crawl was success or not
     */
    private function crawlUrl(string $url): bool
    {
        try {
            return \file_get_contents($url) !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getSleepTime(InputInterface $input): int
    {
        return 1000000 * (int) $input->getOption('sleep');
    }
}
