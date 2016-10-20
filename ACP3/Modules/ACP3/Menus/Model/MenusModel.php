<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Menus\Installer\Schema;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenusModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Secure
     */
    protected $secure;

    /**
     * MenusModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataProcessor $dataProcessor
     * @param Secure $secure
     * @param MenuRepository $menuRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        Secure $secure,
        MenuRepository $menuRepository)
    {
        parent::__construct($eventDispatcher, $dataProcessor, $menuRepository);

        $this->secure = $secure;
    }

    /**
     * @param array $formData
     * @param null|int $menuId
     * @return mixed
     */
    public function saveMenu(array $formData, $menuId = null)
    {
        $values = [
            'index_name' => $formData['index_name'],
            'title' => $this->secure->strEncode($formData['title']),
        ];

        return $this->save($values, $menuId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'index_name',
            'title'
        ];
    }
}
