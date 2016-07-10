<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    {include file="asset:System/Partials/head.tpl" inline}
</head>

<body>
<div class="container-fluid">
    <h1 id="logo" class="hidden-xs">
        {if $IS_HOMEPAGE}
            <img src="{$DESIGN_PATH}Assets/img/logo.png"
                 srcset="{$DESIGN_PATH}Assets/img/logo.png 1x, {$DESIGN_PATH}Assets/img/logo@2x.png 2x"
                 alt="{site_title}">
        {else}
            <a href="{uri args=""}">
                <img src="{$DESIGN_PATH}Assets/img/logo.png"
                     srcset="{$DESIGN_PATH}Assets/img/logo.png 1x, {$DESIGN_PATH}Assets/img/logo@2x.png 2x"
                     alt="{site_title}">
            </a>
        {/if}
    </h1>
    <nav id="main-navigation" class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="{$ROOT_DIR}" class="navbar-brand hidden-sm hidden-md hidden-lg">
                    <img src="{$DESIGN_PATH}Assets/img/logo.png"
                         srcset="{$DESIGN_PATH}Assets/img/logo.png 1x, {$DESIGN_PATH}Assets/img/logo@2x.png 2x"
                         alt="{site_title}">
                </a>
            </div>
            <div id="navbar-collapse" class="collapse navbar-collapse">
                {navbar block="main"}
                {load_module module="widget/search"}
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
            {load_module module="widget/users/index/user_menu"}
            {load_module module="widget/users/index/login"}
        </div>
        <main role="main" id="content" class="col-sm-9 col-md-8">
            <div id="breadcrumb">
                {block BREADCRUMB}
                    {include file="asset:System/Partials/breadcrumb.tpl" breadcrumb=$BREADCRUMB}
                {/block}
            </div>
            <h2>{page_title}</h2>
            {block CONTENT}{/block}
        </main>
        <div class="col-md-2 hidden-xs hidden-sm">
            {load_module module="widget/news"}
            {load_module module="widget/newsletter"}
            {load_module module="widget/files"}
            {load_module module="widget/articles"}
            {load_module module="widget/articles/index/single" args=['id' => 1]}
            {load_module module="widget/gallery"}
            {load_module module="widget/polls"}
        </div>
    </div>
</div>
<!-- JAVASCRIPTS -->
</body>
</html>
