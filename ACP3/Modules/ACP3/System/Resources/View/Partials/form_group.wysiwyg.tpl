{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_LABEL_ID}for="{$name|replace:'_':'-'}"{/block}
{block FORM_GROUP_FORM_FIELD}
    {$toolbar=(!empty($toolbar)) ? $toolbar : ''}
    {$advanced=(isset($advanced)) ? $advanced : false}
    {$value=(isset($value)) ? $value : ''}
    {$editor=(isset($editor)) ? $editor : null}
    {wysiwyg name=$name value=$value toolbar=$toolbar editor=$editor advanced=$advanced}
{/block}
