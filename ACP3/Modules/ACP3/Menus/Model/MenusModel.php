<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Menus\Cache;
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
     * @var Cache
     */
    protected $menusCache;

    /**
     * MenusModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Secure $secure
     * @param MenuRepository $menuRepository
     * @param Cache $menusCache
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Secure $secure,
        MenuRepository $menuRepository,
        Cache $menusCache)
    {
        parent::__construct($eventDispatcher, $menuRepository);

        $this->secure = $secure;
        $this->menusCache = $menusCache;
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

        $result = $this->save($values, $menuId);

        $this->menusCache->saveMenusCache();

        return $result;
    }
}
