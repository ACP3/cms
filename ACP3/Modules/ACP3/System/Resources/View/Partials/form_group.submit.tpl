{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    {block FORM_GROUP_FORM_FIELD_SUBMIT}
        <button type="{if !empty($button_type)}{$button_type}{else}submit{/if}"
                name="{if isset($name)}{$name}{else}name{/if}"
                class="btn{if !empty($submit_btn_class)} {$submit_btn_class}{else} btn-primary{/if}"
                {if !empty($attributes) && is_array($attributes)}
                    {foreach $attributes as $attrName => $attrValue}
                        {$attrName}="{$attrValue}"
                    {/foreach}
                {/if}>
            {if !empty($submit_label)}
                {$submit_label}
            {else}
                {lang t="system|submit"}
            {/if}
        </button>
    {/block}
    {if !empty($back_url)}
        <a href="{$back_url}" class="btn{if !empty($back_btn_class)} {$back_btn_class}{else} btn-light{/if}">
            {if !empty($back_label)}
                {$back_label}
            {else}
                {lang t="system|cancel"}
            {/if}
        </a>
    {/if}
    {if !empty($form_token)}
        {$form_token}
    {/if}
{/block}
