{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <nav id="adm-list" class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex2-collapse">
                <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                <span class="icon-bar"></span> <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse navbar-ex2-collapse">
            <div class="navbar-text pull-right">
                {check_access mode="link" path="users/account/edit" class="glyphicon glyphicon-pencil text-info"}
                {check_access mode="link" path="users/account/settings" class="glyphicon glyphicon-cog"}
            </div>
        </div>
    </nav>
    {redirect_message}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="draft" class="col-sm-2 control-label">{lang t="users|drafts"}</label>

            <div class="col-sm-10">
                {wysiwyg name="draft" value="$draft" height="250" toolbar="simple"}
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
