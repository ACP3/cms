{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_BEFORE_AJAX_FORM}
    {redirect_message}
{/block}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="nickname" required=true maxlength=30 label={lang t="users|nickname"}}
    {include file="asset:System/Partials/form_group.input_password.tpl" name="pwd" required=true label={lang t="users|pwd"}}
    {include file="asset:System/Partials/form_group.checkbox.tpl" options=$remember_me}
    {include file="asset:System/Partials/form_group.submit.tpl" submit_label={lang t="users|log_in"} back_url={uri args="users/index/forgot_pwd"} back_label={lang t="users|forgot_pwd"} back_btn_class='btn-link'}
{/block}
