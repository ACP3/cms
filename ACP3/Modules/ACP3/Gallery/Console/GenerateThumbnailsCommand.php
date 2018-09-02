<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Console;

use ACP3\Core\Cache\Purge;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateThumbnailsCommand extends Command
{
    /**
     * @var GalleryRepository
     */
    private $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $galleryPicturesRepository;
    /**
     * @var ThumbnailGenerator
     */
    private $thumbnailGenerator;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;

    public function __construct(
        ApplicationPath $appPath,
        ThumbnailGenerator $thumbnailGenerator,
        ContainerInterface $container,
        GalleryRepository $galleryRepository,
        PictureRepository $galleryPicturesRepository
    ) {
        parent::__construct();

        $this->galleryRepository = $galleryRepository;
        $this->galleryPicturesRepository = $galleryPicturesRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->container = $container;
        $this->appPath = $appPath;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('acp3:gallery:generate-thumbnails')
            ->setDescription('Generates the gallery pictures thumbnails.')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Whether to delete all previously generated cached pictures'
            );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Generating the gallery picture thumbnails...');

        $this->cleanCachedPictures($input, $output);

        ProgressBar::setFormatDefinition('custom', ' %current%/%max% -- %message%');

        foreach ($this->galleryRepository->getAll() as $gallery) {
            $output->write("Generating thumbnails for gallery `{$gallery['title']}`:");

            $pictures = $this->galleryPicturesRepository->getPicturesByGalleryId($gallery['id']);
            $progress = new ProgressBar($output, \count($pictures));
            $progress->setFormat('custom');

            foreach ($pictures as $picture) {
                $progress->setMessage($picture['file']);
                $this->generateThumbnails($picture['file']);
                $progress->advance();
            }

            $progress->finish();
            $output->writeln('');
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function cleanCachedPictures(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getOption('force')) {
            $output->writeln('Deleting cached gallery pictures:');
            Purge::doPurge($this->appPath->getUploadsDir() . Schema::MODULE_NAME . '/cache/');
            $output->writeln('Done!');
        }
    }

    /**
     * @param string $pictureFileName
     */
    private function generateThumbnails(string $pictureFileName)
    {
        $this->generateThumbnail($pictureFileName, 'thumb');
        $this->generateThumbnail($pictureFileName, '');
    }

    /**
     * @param string $pictureFileName
     * @param string $action
     *
     * @return bool
     */
    private function generateThumbnail(string $pictureFileName, string $action): bool
    {
        $image = $this->container->get('core.image');
        $this->thumbnailGenerator->generateThumbnail($image, $action, $pictureFileName);

        return $image->process();
    }
}
