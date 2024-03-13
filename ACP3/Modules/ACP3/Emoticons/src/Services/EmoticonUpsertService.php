<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Services;

use ACP3\Core\Helpers\Upload;
use ACP3\Modules\ACP3\Emoticons\Model\EmoticonsModel;
use ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EmoticonUpsertService
{
    public function __construct(private readonly Upload $emoticonsUploadHelper, private readonly EmoticonsModel $emoticonsModel, private readonly AdminFormValidation $adminFormValidation)
    {
    }

    /**
     * @param array<string, mixed> $updatedData
     *
     * @throws \ACP3\Core\Validation\Exceptions\InvalidFormTokenException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function upsert(array $updatedData, ?UploadedFile $file, ?int $emoticonId = null): int
    {
        $this->adminFormValidation
            ->withFile($file, $emoticonId === null)
            ->validate($updatedData);

        if ($file !== null) {
            $result = $this->emoticonsUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());
            $updatedData['img'] = $result['name'];

            if ($emoticonId !== null) {
                $currentData = $this->emoticonsModel->getOneById($emoticonId);
                $this->emoticonsUploadHelper->removeUploadedFile($currentData['img']);
            }
        }

        return $this->emoticonsModel->save($updatedData, $emoticonId);
    }
}
