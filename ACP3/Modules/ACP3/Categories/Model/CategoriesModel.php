<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Categories\Installer\Schema;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CategoriesModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Secure
     */
    protected $secure;

    /**
     * CategoriesModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Secure $secure
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Secure $secure,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($eventDispatcher, $categoryRepository);

        $this->secure = $secure;
    }

    /**
     * @param array $formData
     * @param int|null $entryId
     * @return bool|int
     */
    public function saveCategory(array $formData, $entryId = null)
    {
        $data = [
            'title' => $this->secure->strEncode($formData['title']),
            'description' => $this->secure->strEncode($formData['description']),
        ];

        if (isset($formData['module'])) {
            $data['module_id'] = (int)$formData['module'];
        }
        if (isset($formData['picture'])) {
            $data['picture'] = $formData['picture'];
        }

        return $this->save($data, $entryId);
    }
}
