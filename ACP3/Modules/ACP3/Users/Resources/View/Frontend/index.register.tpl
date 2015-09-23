{extends file="asset:layout.tpl"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab-1" data-toggle="tab">{lang t="users|nickname"} &amp; {lang t="system|email_address"}</a>
                </li>
                <li><a href="#tab-2" data-toggle="tab">{lang t="users|pwd"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in active">
                    <div class="form-group">
                        <label for="nickname" class="col-sm-2 control-label required">{lang t="users|nickname"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="nickname" id="nickname" value="{$form.nickname}" maxlength="30" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mail" class="col-sm-2 control-label required">{lang t="system|email_address"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120" required>
                        </div>
                    </div>
                </div>
                <div id="tab-2" class="tab-pane fade">
                    <div class="form-group">
                        <label for="pwd" class="col-sm-2 control-label required">{lang t="users|pwd"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="password" name="pwd" id="pwd" value="" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pwd-repeat" class="col-sm-2 control-label required">{lang t="users|pwd_repeat"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="password" name="pwd_repeat" id="pwd-repeat" value="" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {if isset($captcha)}
            {$captcha}
        {/if}
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}