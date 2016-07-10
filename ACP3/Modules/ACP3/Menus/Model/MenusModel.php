<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Menus\Cache;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository;

class MenusModel extends AbstractModel
{
    /**
     * @var Secure
     */
    protected $secure;
    /**
     * @var MenuRepository
     */
    protected $menuRepository;
    /**
     * @var Cache
     */
    protected $menusCache;

    /**
     * MenusModel constructor.
     * @param Secure $secure
     * @param MenuRepository $menuRepository
     * @param Cache $menusCache
     */
    public function __construct(
        Secure $secure,
        MenuRepository $menuRepository,
        Cache $menusCache)
    {
        $this->secure = $secure;
        $this->menuRepository = $menuRepository;
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

        $result = $this->save($this->menuRepository, $values, $menuId);

        $this->menusCache->saveMenusCache();

        return $result;
    }
}
