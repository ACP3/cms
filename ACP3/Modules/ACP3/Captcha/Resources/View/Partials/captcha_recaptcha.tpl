{extends file="asset:System/Partials/form_group.base.tpl"}

{$required=true}
{if !$captcha.input_only}
    {$label={lang t="captcha|captcha"}}
{else}
    {$cssSelector=''}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$captcha.id}"{/block}
{block FORM_GROUP_FORM_FIELD}
    <div id="{$captcha.id}" class="recaptcha-placeholder" data-sitekey="{$captcha.sitekey}"></div>
{/block}
