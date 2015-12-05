<?php
namespace ACP3\Modules\ACP3\Articles\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Articles\Model\ArticleRepository;

/**
 * Class ArticleExistsValidationRule
 * @package ACP3\Modules\ACP3\Articles\Validation\ValidationRules
 */
class ArticleExistsValidationRule extends AbstractValidationRule
{
    const NAME = 'articles_article_exists';

    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\ArticleRepository
     */
    protected $articleRepository;

    /**
     * ArticleExistsValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Articles\Model\ArticleRepository $articleRepository
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->articleRepository->resultExists($data);
    }
}