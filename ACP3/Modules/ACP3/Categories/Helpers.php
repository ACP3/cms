<?php
namespace ACP3\Modules\ACP3\Categories;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Categories
 */
class Helpers
{
    /**
     * @var Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var Core\View
     */
    protected $view;
    /**
     * @var Cache
     */
    protected $categoriesCache;
    /**
     * @var Model
     */
    protected $categoriesModel;
    /**
     * @var Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * @param \ACP3\Core\ACL                      $acl
     * @param \ACP3\Core\Lang                     $lang
     * @param \ACP3\Core\Modules                  $modules
     * @param \ACP3\Core\Http\RequestInterface         $request
     * @param \ACP3\Core\View                     $view
     * @param \ACP3\Core\Helpers\Forms            $formsHelper
     * @param \ACP3\Modules\ACP3\Categories\Cache $categoriesCache
     * @param \ACP3\Modules\ACP3\Categories\Model $categoriesModel
     */
    public function __construct(
        Core\ACL $acl,
        Core\Lang $lang,
        Core\Modules $modules,
        Core\Http\RequestInterface $request,
        Core\View $view,
        Core\Helpers\Forms $formsHelper,
        Cache $categoriesCache,
        Model $categoriesModel
    )
    {
        $this->acl = $acl;
        $this->lang = $lang;
        $this->modules = $modules;
        $this->request = $request;
        $this->view = $view;
        $this->formsHelper = $formsHelper;
        $this->categoriesCache = $categoriesCache;
        $this->categoriesModel = $categoriesModel;
    }

    /**
     * Überprüft, ob eine Kategorie existiert
     *
     * @param integer $categoryId
     *
     * @return boolean
     */
    public function categoryExists($categoryId)
    {
        return $this->categoriesModel->resultExists($categoryId);
    }

    /**
     * Erzeugt eine neue Kategorie und gibt ihre ID zurück
     *
     * @param string $title
     * @param string $module
     *
     * @return integer
     */
    public function categoriesCreate($title, $module)
    {
        if ($this->categoryIsDuplicate($title, $module) === false) {
            $moduleInfo = $this->modules->getModuleInfo($module);

            $insertValues = [
                'id' => '',
                'title' => Core\Functions::strEncode($title),
                'picture' => '',
                'description' => '',
                'module_id' => $moduleInfo['id'],
            ];
            $result = $this->categoriesModel->insert($insertValues);

            $this->categoriesCache->setCache($module);

            return $result;
        } else {
            return $this->categoriesModel->getOneByTitleAndModule($title, $module)['id'];
        }
    }

    /**
     * Überprüft, ob bereits eine Kategorie mit dem selben Namen existiert
     *
     * @param string     $title
     * @param string     $module
     * @param int|string $categoryId
     *
     * @return boolean
     */
    public function categoryIsDuplicate($title, $module, $categoryId = '')
    {
        return $this->categoriesModel->resultIsDuplicate($title, $module, $categoryId);
    }

    /**
     * Listet alle Kategorien eines Moduls auf
     *
     * @param string  $module
     * @param string  $categoryId
     * @param boolean $categoryCreate
     * @param string  $formFieldName
     * @param string  $customText
     *
     * @return string
     */
    public function categoriesList($module, $categoryId = '', $categoryCreate = false, $formFieldName = 'cat', $customText = '')
    {
        $categories = [];
        $data = $this->categoriesCache->getCache($module);
        $c_data = count($data);

        $categories['custom_text'] = !empty($customText) ? $customText : $this->lang->t('system', 'pls_select');
        $categories['name'] = $formFieldName;
        if ($c_data > 0) {
            for ($i = 0; $i < $c_data; ++$i) {
                $data[$i]['selected'] = $this->formsHelper->selectEntry('cat', $data[$i]['id'], $categoryId);
            }
            $categories['categories'] = $data;
        } else {
            $categories['categories'] = [];
        }
        if ($categoryCreate === true && $this->acl->hasPermission('admin/categories/index/create') === true) {
            $categories['create']['name'] = $formFieldName . '_create';
            $categories['create']['value'] = $this->request->getPost()->get('create', ['name' => ''])['name'];
        }
        $this->view->assign('categories', $categories);
        return $this->view->fetchTemplate('categories/create_list.tpl');
    }
}
