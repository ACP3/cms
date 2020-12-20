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
    {if $captcha.includeJsAssets}
        {javascripts}
            {include_js module="captcha" file="partials/recaptcha.onload"}
            <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&amp;render=explicit&amp;hl={$LANG}"
                    async
                    defer></script>
        {/javascripts}
    {/if}
{/block}
