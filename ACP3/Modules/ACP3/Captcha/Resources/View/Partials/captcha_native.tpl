{extends file="asset:System/Partials/form_group.base.tpl"}

{$required=true}
{if !$captcha.input_only}
    {$label={lang t="captcha|captcha"}}
{else}
    {$cssSelector=''}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$captcha.id}"{/block}
{block FORM_GROUP_FORM_FIELD}
    <img src="{load_module module="frontend/captcha/index/image/" args=["token" => $captcha.token]}"
         width="{$captcha.width}"
         height="{$captcha.height}"
         class="mb-2"
         alt="">
    <input class="form-control"
           type="text"
           name="captcha"
           id="{$captcha.id}"
           value=""
           required>
    <input type="hidden"
           name="captcha_token"
           value="{$captcha.token}">
{/block}
