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
            {check_access mode="link" path="users/edit_profile" icon="32/edit_user" width="32" height="32"}
            {check_access mode="link" path="users/edit_settings" icon="32/advancedsettings" width="32" height="32"}
        </div>
    </div>
</nav>{if isset($redirect_message)}
    {$redirect_message}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal ajax-form">
    <div class="form-group">
        <label for="draft" class="col-lg-2 control-label">{lang t="users|drafts"}</label>

        <div class="col-lg-10">
            {wysiwyg name="draft" value="$draft" height="250" toolbar="simple"}
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
        </div>
    </div>
</form>
{include_js module="system" file="forms"}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.ajax-form').formSubmit('{lang t="system|loading_please_wait"}');
    });
</script>