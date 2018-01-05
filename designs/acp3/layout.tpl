<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    {include file="asset:System/Partials/head.tpl" inline}
</head>

<body>
{load_module module="widget/users/index/login"}
{load_module module="widget/users/index/user_menu"}
<div id="wrapper" class="container">
    <header>
        <h1 class="logo" class="hidden-xs">
            {if $IS_HOMEPAGE}
                <img src="{image file="logo.png"}"
                     srcset="{image file="logo.png"} 1x, {image file="logo@2x.png"} 2x"
                     alt="{site_title}">
            {else}
                <a href="{uri args=""}">
                    <img src="{image file="logo.png"}"
                         srcset="{image file="logo.png"} 1x, {image file="logo@2x.png"} 2x"
                         alt="{site_title}">
                </a>
            {/if}
        </h1>
    </header>
    <nav class="navbar navbar-default navigation-main-wrapper">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="{$ROOT_DIR}" class="navbar-brand hidden-sm hidden-md hidden-lg">
                    <img src="{image file="logo.png"}"
                         srcset="{image file="logo.png"} 1x, {image file="logo@2x.png"} 2x"
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
        <main id="content" class="col-sm-12{if !$IN_ADM} col-md-9{/if}">
            <div id="breadcrumb">
                {block BREADCRUMB}
                    {breadcrumb}
                {/block}
            </div>
            <h1 class="h2">{page_title}</h1>
            {event name="layout.content_before"}
            {block CONTENT}{/block}
        </main>
        {if !$IN_ADM}
            <aside id="sidebar" class="col-md-3 hidden-xs hidden-sm">
                {load_module module="widget/newsletter"}
                {load_module module="widget/news"}
                {load_module module="widget/files"}
                {load_module module="widget/articles"}
                {load_module module="widget/articles/index/single" args=['id' => 1]}
                {load_module module="widget/gallery"}
                {load_module module="widget/polls"}
            </aside>
        {/if}
    </div>
    <footer class="row footer">
        <div class="col-xs-5 copyright">
            &copy; {site_title}
        </div>
        <div class="col-xs-7">
            {navbar block="sidebar" class="list-inline text-right" use_bootstrap=false}
        </div>
    </footer>
</div>
<!-- JAVASCRIPTS -->
</body>
</html>
