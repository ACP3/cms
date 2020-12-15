{extends file="asset:System/Partials/form_group.base.tpl"}

{$required=true}
{if $captcha.input_only}
    {$cssSelector=''}
{/if}

{block FORM_GROUP_FORM_FIELD}
    <div id="recaptcha-{$captcha.id}"
         class="recaptcha-placeholder"
         data-sitekey="{$captcha.sitekey}"{if $captcha.length < 5} data-size="compact"{/if}></div>
    <div id="{$captcha.id}"></div>
{/block}
