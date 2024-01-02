{$LAYOUT='System/layout.simple.tpl'}

{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_BEFORE_AJAX_FORM}
    {redirect_message}
{/block}

{block CONTENT_AJAX_FORM_URI}{uri args="users/index/login/redirect_`$redirect_uri`"}{/block}

{block CONTENT_AJAX_FORM}
    <div class="form-floating">
        <input type="text" class="form-control" id="nickname" name="nickname" placeholder="{lang t="users|nickname"}">
        <label for="nickname">{lang t="users|nickname"}</label>
    </div>
    <div class="form-floating">
        <input type="password" class="form-control" id="pwd" name="pwd" placeholder="{lang t="users|pwd"}">
        <label for="pwd">{lang t="users|pwd"}</label>
    </div>
    <div class="form-check text-start my-3">
        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
        <label class="form-check-label" for="remember">
            {lang t="users|remember_me"}
        </label>
    </div>
    <button class="btn btn-primary w-100 py-2" type="submit" name="submit">{lang t="system|submit"}</button>
    <div class="text-center mt-2">
        <a href="{uri args="users/index/forgot_pwd"}" class="btn btn-link">{lang t="users|forgot_pwd"}</a>
    </div>
{/block}
