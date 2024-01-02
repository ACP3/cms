{extends file="asset:System/Partials/form_group.base.tpl"}

{$floatingLabel=$captcha.floatingLabel}

{$required=true}
{if !$captcha.inputOnly}
    {$label={lang t="captcha|captcha"}}
{else}
    {$cssSelector=''}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$captcha.id}"{/block}
{block FORM_GROUP_FORM_FIELD}
    <input class="form-control"
           type="text"
           name="captcha"
           id="{$captcha.id}"
           value=""
           required>
{/block}

{block FORM_GROUP_FORM_FIELD_EXTENSION}
    {load_module module="frontend/captcha/index/image/" args=["token" => $captcha.token, "width" => $captcha.width, "height" => $captcha.height]}
    <input type="hidden"
           name="captcha_token"
           value="{$captcha.token}">
{/block}
