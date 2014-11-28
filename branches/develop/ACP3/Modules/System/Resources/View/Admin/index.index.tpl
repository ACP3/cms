{extends file="asset:layout.tpl"}

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
                {check_access mode="link" path="acp/system/maintenance" icon="32/package_utilities" width="32" height="32"}
                {check_access mode="link" path="acp/system/extensions" icon="32/package_applications" width="32" height="32"}
                {check_access mode="link" path="acp/system/index/configuration" class="glyphicon glyphicon-wrench"}
            </div>
        </div>
    </nav>
    <div class="alert alert-warning text-center">
        <strong>{lang t="system|select_menu_item"}</strong>
    </div>
{/block}