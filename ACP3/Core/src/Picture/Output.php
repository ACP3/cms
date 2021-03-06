<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Picture;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Picture\Exception\PictureResponseException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Output
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var int
     */
    private $type;
    /**
     * @var string
     */
    private $srcFile;
    /**
     * @var int|null
     */
    private $srcWidth;
    /**
     * @var int|null
     */
    private $srcHeight;
    /**
     * @var string|null
     */
    private $destFile;
    /**
     * @var int|null
     */
    private $destWidth;
    /**
     * @var int|null
     */
    private $destHeight;

    public function __construct(ApplicationPath $appPath, string $srcFile, int $type)
    {
        $this->appPath = $appPath;
        $this->srcFile = $srcFile;
        $this->type = $type;
    }

    public function getSrcFile(): string
    {
        return $this->srcFile;
    }

    public function getSrcWidth(): ?int
    {
        return $this->srcWidth;
    }

    /**
     * @return $this
     */
    public function setSrcWidth(?int $srcWidth): self
    {
        $this->srcWidth = $srcWidth;

        return $this;
    }

    public function getSrcHeight(): ?int
    {
        return $this->srcHeight;
    }

    /**
     * @return $this
     */
    public function setSrcHeight(?int $srcHeight): self
    {
        $this->srcHeight = $srcHeight;

        return $this;
    }

    public function getDestFile(): ?string
    {
        return $this->destFile;
    }

    /**
     * @return $this
     */
    public function setDestFile(string $destFile): self
    {
        $this->destFile = $destFile;

        return $this;
    }

    public function getDestWidth(): ?int
    {
        return $this->destWidth;
    }

    /**
     * @return $this
     */
    public function setDestWidth(?int $destWidth): self
    {
        $this->destWidth = $destWidth;

        return $this;
    }

    public function getDestHeight(): ?int
    {
        return $this->destHeight;
    }

    /**
     * @return $this
     */
    public function setDestHeight(?int $destHeight): self
    {
        $this->destHeight = $destHeight;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getFileWeb(): string
    {
        return $this->appPath->getWebRoot() . substr(str_replace(ACP3_ROOT_DIR, '', $this->getFile()), 1);
    }

    public function getFile(): string
    {
        return $this->getDestFile() ?? $this->getSrcFile();
    }

    public function getWidth(): int
    {
        return $this->getDestWidth() ?? $this->getSrcWidth();
    }

    public function getHeight(): int
    {
        return $this->getDestHeight() ?? $this->getSrcHeight();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \ACP3\Core\Picture\Exception\PictureResponseException
     */
    public function sendResponse()
    {
        $response = new BinaryFileResponse($this->getFile());
        $this->setHeaders($response, $this->getMimeType($this->getType()));

        return $response;
    }

    /**
     * @return string
     *
     * @throws \ACP3\Core\Picture\Exception\PictureResponseException
     */
    private function getMimeType(int $pictureType)
    {
        switch ($pictureType) {
            case IMAGETYPE_GIF:
                return 'image/gif';
            case IMAGETYPE_JPEG:
                return 'image/jpeg';
            case IMAGETYPE_PNG:
                return 'image/png';
        }

        throw new PictureResponseException(sprintf('Unsupported picture type: %s', $pictureType));
    }

    private function setHeaders(BinaryFileResponse $response, string $mimeType)
    {
        $response->headers->add([
            'Content-type' => $mimeType,
            'Cache-Control' => 'public',
            'Pragma' => 'public',
            'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($this->getFile())) . ' GMT',
            'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
        ]);
    }
}
