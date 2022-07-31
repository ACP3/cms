<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureValidationRuleTest extends TestCase
{
    private PictureValidationRule $pictureValidationRule;
    private MockObject|FileUploadValidationRule $fileUploadValidationRuleMock;

    /**
     * @return array<string, array<string, mixed>[]>
     */
    public function providerDimensionConstraints(): array
    {
        return [
            'width-out-of-bounds' => [
                [
                    'width' => 100,
                ],
            ],
            'height-out-of-bounds' => [
                [
                    'height' => 100,
                ],
            ],
            'filesize-out-of-bounds' => [
                [
                    'filesize' => 1,
                ],
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileUploadValidationRuleMock = $this->createMock(FileUploadValidationRule::class);

        $this->pictureValidationRule = new PictureValidationRule($this->fileUploadValidationRuleMock);
    }

    public function testIsValid(): void
    {
        $uploadedFile = new UploadedFile(\dirname(__DIR__, 3) . '/fixtures/150.png', '150.png', 'image/png');

        $this->fileUploadValidationRuleMock->method('isValid')
            ->with($uploadedFile)
            ->willReturn(true);

        self::assertTrue($this->pictureValidationRule->isValid($uploadedFile));
    }

    public function testIsValidWithUploadError(): void
    {
        $uploadedFile = new UploadedFile(\dirname(__DIR__, 3) . '/fixtures/150.png', '150.png', 'image/png', UPLOAD_ERR_INI_SIZE);

        $this->fileUploadValidationRuleMock->method('isValid')
            ->with($uploadedFile)
            ->willReturn(false);

        self::assertFalse($this->pictureValidationRule->isValid($uploadedFile));
    }

    public function testIsValidWithArray(): void
    {
        $uploadedFile = [
            'tmp_name' => \dirname(__DIR__, 3) . '/fixtures/150.png',
            'size' => filesize(\dirname(__DIR__, 3) . '/fixtures/150.png'),
            'error' => UPLOAD_ERR_OK,
        ];

        $this->fileUploadValidationRuleMock->method('isValid')
            ->with($uploadedFile)
            ->willReturn(true);

        self::assertTrue($this->pictureValidationRule->isValid($uploadedFile));
    }

    public function testIsValidWithNoOptionalFile(): void
    {
        $this->fileUploadValidationRuleMock->expects(self::never())
            ->method('isValid');

        self::assertTrue($this->pictureValidationRule->isValid(null, '', ['required' => false]));
    }

    /**
     * @dataProvider providerDimensionConstraints
     *
     * @param array<string, mixed> $extra
     */
    public function testIsValidWithDimensionConstraints(array $extra): void
    {
        $uploadedFile = new UploadedFile(\dirname(__DIR__, 3) . '/fixtures/150.png', '150.png', 'image/png');

        $this->fileUploadValidationRuleMock->method('isValid')
            ->with($uploadedFile)
            ->willReturn(true);

        self::assertFalse($this->pictureValidationRule->isValid($uploadedFile, '', $extra));
    }
}
