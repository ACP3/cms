<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Console;

use ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesRepository;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateThumbnailsCommand extends Command
{
    /**
     * @var GalleryRepository
     */
    private $galleryRepository;
    /**
     * @var GalleryPicturesRepository
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
     * CacheGalleryImagesCommand constructor.
     *
     * @param ThumbnailGenerator        $thumbnailGenerator
     * @param ContainerInterface        $container
     * @param GalleryRepository         $galleryRepository
     * @param GalleryPicturesRepository $galleryPicturesRepository
     */
    public function __construct(
        ThumbnailGenerator $thumbnailGenerator,
        ContainerInterface $container,
        GalleryRepository $galleryRepository,
        GalleryPicturesRepository $galleryPicturesRepository
    ) {
        parent::__construct();

        $this->galleryRepository = $galleryRepository;
        $this->galleryPicturesRepository = $galleryPicturesRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('acp3:gallery:generate-thumbnails')
            ->setDescription('Generates the gallery pictures thumbnails.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Generating the gallery picture thumbnails...');

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
