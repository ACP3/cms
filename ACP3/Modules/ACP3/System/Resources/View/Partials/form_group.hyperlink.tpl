{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    <a href="{$href}"
       {if !empty($attributes) && is_array($attributes)}
            {foreach $attributes as $attrName => $attrValue}
                {$attrName}="{$attrValue}"
            {/foreach}
       {/if}>
        {$hyperlinkLabel}
    </a>
{/block}
