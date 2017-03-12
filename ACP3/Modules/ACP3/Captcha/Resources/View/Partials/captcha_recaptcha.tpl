{extends file="asset:System/Partials/form_group.base.tpl"}

{$required=true}
{if $captcha.input_only}
    {$cssSelector=''}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$captcha.id}"{/block}
{block FORM_GROUP_FORM_FIELD}
    <div id="{$captcha.id}" class="recaptcha-placeholder"{if $captcha.length < 5}data-size="compact"{/if}></div>
{/block}
