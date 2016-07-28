<?php
namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Modules;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Core\Validation\ValidationRules\InternalUriValidationRule;
use ACP3\Modules\ACP3\Articles\Validation\ValidationRules\ArticleExistsValidationRule;

/**
 * Class LinkModeValidationRule
 * @package ACP3\Modules\ACP3\Menus\Validation\ValidationRules
 */
class LinkModeValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Validation\ValidationRules\InternalUriValidationRule
     */
    protected $internalUriValidationRule;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Validation\ValidationRules\ArticleExistsValidationRule
     */
    protected $articleExistsValidationRule;

    /**
     * LinkModeValidationRule constructor.
     *
     * @param \ACP3\Core\Modules                                              $modules
     * @param \ACP3\Core\Validation\ValidationRules\InternalUriValidationRule $internalUriValidationRule
     */
    public function __construct(
        Modules $modules,
        InternalUriValidationRule $internalUriValidationRule
    ) {
        $this->modules = $modules;
        $this->internalUriValidationRule = $internalUriValidationRule;
    }

    /**
     * @param \ACP3\Modules\ACP3\Articles\Validation\ValidationRules\ArticleExistsValidationRule $articleExistsValidationRule
     *
     * @return $this
     */
    public function setArticleExistsValidationRule(ArticleExistsValidationRule $articleExistsValidationRule)
    {
        $this->articleExistsValidationRule = $articleExistsValidationRule;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && is_array($field)) {
            $mode = reset($field);
            $moduleName = next($field);
            $uri = next($field);
            $articleId = next($field);

            return $this->isValidLink($data[$mode], $data[$moduleName], $data[$uri], $data[$articleId]);
        }

        return false;
    }

    /**
     * @param string $mode
     * @param string $moduleName
     * @param string $uri
     * @param int    $articleId
     *
     * @return bool
     */
    protected function isValidLink($mode, $moduleName, $uri, $articleId)
    {
        switch ($mode) {
            case 1:
                return $this->modules->isInstalled($moduleName);
            case 2:
                return $this->internalUriValidationRule->isValid($uri);
            case 3:
                return !empty($uri);
            case 4:
                if ($this->modules->isInstalled('articles')) {
                    return $this->articleExistsValidationRule->isValid($articleId);
                }
        }

        return false;
    }
}
