{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="nickname" class="col-sm-2 control-label required">{lang t="users|nickname"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="nickname" id="nickname" maxlength="30" required></div>
        </div>
        <div class="form-group">
            <label for="pwd" class="col-sm-2 control-label required">{lang t="users|pwd"}</label>

            <div class="col-sm-10"><input class="form-control" type="password" name="pwd" id="pwd" required></div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label for="remember"> <input type="checkbox" name="remember" id="remember" value="1">
                        {lang t="users|remember_me"}
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="glyphicon glyphicon-lock"></i> {lang t="users|log_in"}</button>
                <a href="{uri args="users/index/forgot_pwd"}" class="btn btn-link">{lang t="users|forgot_pwd"}</a>
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
