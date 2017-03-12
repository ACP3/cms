{extends file="asset:System/Partials/form_group.base.tpl"}

{$required=true}
{if $captcha.input_only}
    {$cssSelector=''}
{/if}

{block FORM_GROUP_FORM_FIELD}
    <div id="{$captcha.id}"
         class="recaptcha-placeholder"
         data-size="invisible">
    </div>
    <div class="recaptcha-placeholder"></div>
{/block}
