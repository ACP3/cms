{if $IS_AJAX}
    {include file="asset:system/ajax.tpl" inline}
{else}
    <!DOCTYPE html>
    <html lang="{$LANG}" dir="{$LANG_DIRECTION}">
    <head>
        {include file="asset:system/head.tpl" inline}
    </head>

    <body>
    <div class="container-fluid">
        <h1 id="logo" class="hidden-xs"><a href="{uri args=""}">{$PAGE_TITLE}</a></h1>
        <nav class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                        <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="{$ROOT_DIR}" class="navbar-brand hidden-sm hidden-md hidden-lg">{$PAGE_TITLE}</a>
                </div>
                <div id="navbar-collapse" class="collapse navbar-collapse">
                    {navbar block="main"}
                    {load_module module="sidebar/search"}
                </div>
            </div>
        </nav>
        <div class="row">
            <div class="col-sm-3 col-md-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{lang t="system|navigation"}</h3>
                    </div>
                    {navbar block="sidebar" class="list-group" classLink="list-group-item" dropdownItemClass="dropdown" itemTag="" dropdownWrapperTag="div" tag="div"}
                </div>
                {load_module module="sidebar/users/index/user_menu"}
                {load_module module="sidebar/users/index/login"}
            </div>
            <main role="main" id="content" class="col-sm-9 col-md-8">
                <div id="breadcrumb">
                    {block BREADCRUMB}
                        {include file="asset:system/breadcrumb.tpl" breadcrumb=$BREADCRUMB}
                    {/block}
                </div>
                <h2>{$TITLE}</h2>
                {block CONTENT}
                    {$CONTENT}
                {/block}
            </main>
            <div class="col-md-2 hidden-xs hidden-sm">
                {load_module module="sidebar/news"}
                {load_module module="sidebar/newsletter"}
                {load_module module="sidebar/files"}
                {load_module module="sidebar/gallery"}
                {load_module module="sidebar/polls"}
            </div>
        </div>
    </div>
    <!-- JAVASCRIPTS -->
    </body>
    </html>
{/if}